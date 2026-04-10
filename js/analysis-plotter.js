'use strict';

/**
 * Plot result from the beam analysis calculation into a graph
 */
class AnalysisPlotter {
    constructor(container) {
        this.container = container;
    }

    getConfig() {
        if (this.container.indexOf('deflection') !== -1) {
            return {
                yLabel: 'Deflection (mm)',
                xLabel: 'Span (m)'
            };
        }

        if (this.container.indexOf('shear') !== -1) {
            return {
                yLabel: 'Shear Force (kN)',
                xLabel: 'Span (m)'
            };
        }

        return {
            yLabel: 'Bending Moment (kNm)',
            xLabel: 'Span (m)'
        };
    }

    resizeCanvas(canvas) {
        var ratio = window.devicePixelRatio || 1;
        var width = Math.max(320, Math.min(window.innerWidth - 32, 1200));
        var height = width < 480 ? 260 : 420;

        canvas.style.display = 'block';
        canvas.style.margin = '24px 0';
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        canvas.width = Math.floor(width * ratio);
        canvas.height = Math.floor(height * ratio);

        return {
            ratio: ratio,
            width: width,
            height: height
        };
    }

    getSegments(data) {
        if (data.condition === 'two-span-unequal') {
            return [
                { from: 0, to: data.beam.primarySpan, endSide: 'left' },
                {
                    from: data.beam.primarySpan,
                    to: data.beam.primarySpan + data.beam.secondarySpan,
                    startSide: 'right'
                }
            ];
        }

        return [
            { from: 0, to: data.beam.primarySpan }
        ];
    }

    sampleSegments(segments, equation) {
        return segments.map(function (segment) {
            var span = segment.to - segment.from;
            var steps = Math.max(40, Math.round(span * 40));
            var points = [];
            var index;
            var x;
            var side;
            var point;

            for (index = 0; index <= steps; index += 1) {
                x = segment.from + (span * index / steps);
                side = null;

                if (index === 0 && segment.startSide) {
                    side = segment.startSide;
                } else if (index === steps && segment.endSide) {
                    side = segment.endSide;
                }

                point = equation(x, side);

                if (point && Number.isFinite(point.y)) {
                    points.push(point);
                }
            }

            return points;
        }).filter(function (points) {
            return points.length > 0;
        });
    }

    getBounds(sampledSegments) {
        var xValues = [];
        var yValues = [];

        sampledSegments.forEach(function (points) {
            points.forEach(function (point) {
                xValues.push(point.x);
                yValues.push(point.y);
            });
        });

        if (xValues.length === 0 || yValues.length === 0) {
            return null;
        }

        yValues.push(0);

        var xMin = Math.min.apply(null, xValues);
        var xMax = Math.max.apply(null, xValues);
        var yMin = Math.min.apply(null, yValues);
        var yMax = Math.max.apply(null, yValues);
        var range = yMax - yMin;
        var padding = range === 0 ? 1 : range * 0.1;

        return {
            xMin: xMin,
            xMax: xMax,
            yMin: yMin - padding,
            yMax: yMax + padding
        };
    }

    niceStep(range, targetSteps) {
        var rough = range / targetSteps;
        var power = Math.pow(10, Math.floor(Math.log10(rough || 1)));
        var scaled = rough / power;

        if (scaled <= 1) {
            return power;
        }

        if (scaled <= 2) {
            return 2 * power;
        }

        if (scaled <= 5) {
            return 5 * power;
        }

        return 10 * power;
    }

    buildTicks(min, max, targetSteps) {
        var step = this.niceStep(max - min, targetSteps);
        var start = Math.ceil(min / step) * step;
        var ticks = [];
        var value;

        for (value = start; value <= max + step * 0.5; value += step) {
            ticks.push(Number(value.toFixed(10)));
        }

        return {
            step: step,
            values: ticks
        };
    }

    formatTick(value, step) {
        if (Math.abs(step) < 1) {
            return value.toFixed(1);
        }

        return value.toFixed(Math.abs(step) < 10 ? 1 : 0);
    }

    drawGrid(ctx, plotArea, bounds, config) {
        var xTicks = this.buildTicks(bounds.xMin, bounds.xMax, 8);
        var yTicks = this.buildTicks(bounds.yMin, bounds.yMax, 6);
        var self = this;

        ctx.save();
        ctx.strokeStyle = 'rgba(15, 23, 42, 0.12)';
        ctx.fillStyle = 'rgba(15, 23, 42, 0.7)';
        ctx.lineWidth = 1;
        ctx.font = '13px "Segoe UI", Tahoma, sans-serif';

        xTicks.values.forEach(function (tick) {
            var x = self.scaleX(tick, bounds, plotArea);

            ctx.beginPath();
            ctx.moveTo(x, plotArea.top);
            ctx.lineTo(x, plotArea.bottom);
            ctx.stroke();

            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            ctx.fillText(self.formatTick(tick, xTicks.step), x, plotArea.bottom + 8);
        });

        yTicks.values.forEach(function (tick) {
            var y = self.scaleY(tick, bounds, plotArea);

            ctx.beginPath();
            ctx.moveTo(plotArea.left, y);
            ctx.lineTo(plotArea.right, y);
            ctx.stroke();

            ctx.textAlign = 'right';
            ctx.textBaseline = 'middle';
            ctx.fillText(self.formatTick(tick, yTicks.step), plotArea.left - 10, y);
        });

        ctx.strokeStyle = 'rgba(15, 23, 42, 0.18)';
        ctx.strokeRect(
            plotArea.left,
            plotArea.top,
            plotArea.right - plotArea.left,
            plotArea.bottom - plotArea.top
        );

        ctx.textAlign = 'center';
        ctx.textBaseline = 'bottom';
        ctx.fillText(config.xLabel, (plotArea.left + plotArea.right) / 2, plotArea.bottom + 40);

        ctx.translate(24, (plotArea.top + plotArea.bottom) / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        ctx.fillText(config.yLabel, 0, 0);
        ctx.restore();
    }

    scaleX(value, bounds, plotArea) {
        return plotArea.left + (
            (value - bounds.xMin) / (bounds.xMax - bounds.xMin || 1)
        ) * (plotArea.right - plotArea.left);
    }

    scaleY(value, bounds, plotArea) {
        return plotArea.top + (
            (bounds.yMax - value) / (bounds.yMax - bounds.yMin || 1)
        ) * (plotArea.bottom - plotArea.top);
    }

    drawSeries(ctx, sampledSegments, bounds, plotArea) {
        var self = this;
        var zeroY = this.scaleY(0, bounds, plotArea);

        ctx.save();
        ctx.lineWidth = 3;
        ctx.strokeStyle = '#f23d1f';
        ctx.fillStyle = 'rgba(15, 23, 42, 0.14)';
        ctx.lineJoin = 'round';
        ctx.lineCap = 'round';

        sampledSegments.forEach(function (points) {
            ctx.beginPath();
            ctx.moveTo(self.scaleX(points[0].x, bounds, plotArea), zeroY);

            points.forEach(function (point) {
                ctx.lineTo(
                    self.scaleX(point.x, bounds, plotArea),
                    self.scaleY(point.y, bounds, plotArea)
                );
            });

            ctx.lineTo(self.scaleX(points[points.length - 1].x, bounds, plotArea), zeroY);
            ctx.closePath();
            ctx.fill();

            ctx.beginPath();

            points.forEach(function (point, index) {
                var drawX = self.scaleX(point.x, bounds, plotArea);
                var drawY = self.scaleY(point.y, bounds, plotArea);

                if (index === 0) {
                    ctx.moveTo(drawX, drawY);
                } else {
                    ctx.lineTo(drawX, drawY);
                }
            });

            ctx.stroke();
        });

        if (sampledSegments.length > 1) {
            sampledSegments.reduce(function (previous, current) {
                var previousPoint = previous[previous.length - 1];
                var currentPoint = current[0];

                if (Math.abs(previousPoint.x - currentPoint.x) < 1e-9) {
                    ctx.beginPath();
                    ctx.moveTo(
                        self.scaleX(previousPoint.x, bounds, plotArea),
                        self.scaleY(previousPoint.y, bounds, plotArea)
                    );
                    ctx.lineTo(
                        self.scaleX(currentPoint.x, bounds, plotArea),
                        self.scaleY(currentPoint.y, bounds, plotArea)
                    );
                    ctx.stroke();
                }

                return current;
            });
        }

        ctx.restore();
    }

    /**
     * Plot equation.
     *
     * @param {Object{beam : Beam, load : float, equation: Function}}  The equation data
     */
    plot(data) {
        var canvas = document.getElementById(this.container);
        var size;
        var ctx;
        var sampledSegments;
        var bounds;
        var plotArea;

        if (!canvas || !data || typeof data.equation !== 'function') {
            return;
        }

        size = this.resizeCanvas(canvas);
        ctx = canvas.getContext('2d');
        ctx.setTransform(size.ratio, 0, 0, size.ratio, 0, 0);
        ctx.clearRect(0, 0, size.width, size.height);

        sampledSegments = this.sampleSegments(
            this.getSegments(data),
            data.equation
        );

        bounds = this.getBounds(sampledSegments);

        if (!bounds) {
            return;
        }

        plotArea = {
            top: 20,
            right: size.width - 24,
            bottom: size.height - 52,
            left: 76
        };

        this.drawGrid(ctx, plotArea, bounds, this.getConfig());
        this.drawSeries(ctx, sampledSegments, bounds, plotArea);
    }
}
