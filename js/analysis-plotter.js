'use strict';

class AnalysisPlotter {

    constructor(container) {

        this.container = container;

        this.chart = null;
    }

    /**
     * Plot equation
     */
    plot(data) {

        const canvas =
            document.getElementById(this.container);

        // destroy chart lama dulu
        const existingChart =
            Chart.getChart(canvas);

        if (existingChart) {
            existingChart.destroy();
        }

        const ctx =
            canvas.getContext('2d');

        const beam = data.beam;

        const equation = data.equation;

        const maxX =
            beam.primarySpan +
            (beam.secondarySpan || 0);

        const points = [];

        // sample equation
        for (let x = 0; x <= maxX; x += 0.05) {

            const point = equation(x);

            points.push({
                x: point.x,
                y: point.y
            });
        }

        // determine chart type
        let yLabel = 'Result';

        if (this.container.includes('bending')) {
            yLabel = 'Bending Moment (kNm)';
        }

        if (this.container.includes('shear')) {
            yLabel = 'Shear Force (kN)';
        }

        if (this.container.includes('deflection')) {
            yLabel = 'Deflection (mm)';'use strict';

class AnalysisPlotter {

    constructor(container){
        this.container = container;
        this.chart = null;
    }

    plot(data, condition){

        const canvas = document.getElementById(this.container);
        const ctx = canvas.getContext('2d');

        if(this.chart) this.chart.destroy();

        const beam = data.beam;
        const eq = data.equation;

        /* ================= X AXIS FIX ================= */
        const maxX =
            condition === 'two-span-unequal'
                ? beam.primarySpan + (beam.secondarySpan || 0)
                : beam.primarySpan;

        const xStep =
            condition === 'two-span-unequal' ? 1 : 0.5;

        const points = [];

        for(let x=0; x<=maxX; x+=xStep){
            points.push(eq(x));
        }

        /* ================= TYPE ================= */
        const type =
            this.container.includes('bending') ? 'bending' :
            this.container.includes('shear') ? 'shear' :
            'deflection';

        const yLabel =
            type === 'bending' ? 'Bending Moment (kNm)' :
            type === 'shear' ? 'Shear Force (kN)' :
            'Deflection (mm)';

        this.chart = new Chart(ctx,{

            type:'line',

            data:{
                datasets:[{
                    data:points,
                    borderColor:'red',
                    borderWidth:2,
                    pointRadius:0
                }]
            },

            options:{

                scales:{

                    x:{
                        type:'linear',
                        min:0,
                        max:maxX,
                        ticks:{
                            stepSize:xStep
                        }
                    },

                    y:{
                        ticks:{
                            stepSize:2
                        },
                        title:{
                            display:true,
                            text:yLabel
                        }
                    }
                }
            }
        });
    }
}
        }

        // destroy previous chart
        if (this.chart) {
            this.chart.destroy();
        }

        this.chart = new Chart(ctx, {

            type: 'line',

            data: {
                datasets: [{
                    data: points,

                    borderColor: 'red',

                    borderWidth: 2,

                    fill: false,

                    tension: 0.25,

                    pointRadius: 0
                }]
            },

            options: {

                responsive: true,

                plugins: {

                    legend: {
                        display: false
                    }
                },

                scales: {

                    x: {

                        type: 'linear',

                        min: 0,

                        max: maxX,

                        ticks: {

                            stepSize: 0.5
                        },

                        title: {

                            display: true,

                            text: 'Span (m)'
                        },

                        grid: {

                            color: '#e5e5e5'
                        }
                    },

                    y: {

                        ticks: {

                            stepSize: this.getYStep(points),

                        },

                        title: {

                            display: true,

                            text: yLabel
                        },

                        grid: {

                            color: '#e5e5e5'
                        }
                    }
                }
            }
        });
    }

    /**
     * Auto Y step
     */
    getYStep(points) {

        const values =
            points.map(p => p.y);

        const max =
            Math.max(...values);

        const min =
            Math.min(...values);

        const range =
            Math.abs(max - min);

        if (range <= 10) {
            return 1;
        }

        if (range <= 20) {
            return 2;
        }

        if (range <= 50) {
            return 5;
        }

        return 10;
    }
}