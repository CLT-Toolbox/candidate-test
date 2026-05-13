'use strict';

function LayupDrawer() {

    this.canvas = null;
    this.ctx = null;

    this.startX = 80;
    this.startY = 40;

    this.drawWidth = 900;
    this.drawHeight = 500;

    this.scaleY = 1;

    this.layers = [];
}

LayupDrawer.prototype = {

    /**
     * Configure canvas
     */
    init: function (canvas) {

        this.canvas = canvas;
        this.ctx = canvas.getContext('2d');

        // smooth render
        this.ctx.imageSmoothingEnabled = true;
    },

    /**
     * Main function
     */
    drawLayup: function (layup, length = 150) {

        this.layers = Object.values(layup);

        this.length = length;

        this.clear();

        this.calculateScale();

        this.drawGrid();

        this.drawLayers();

        this.drawAxisLabels();
    },

    /**
     * Clear canvas
     */
    clear: function () {

        this.ctx.clearRect(
            0,
            0,
            this.canvas.width,
            this.canvas.height
        );
    },

    /**
     * Calculate scale
     */
    calculateScale: function () {

        let totalThickness = 0;

        this.layers.forEach(layer => {
            totalThickness += layer.thickness;
        });

        this.totalThickness = totalThickness;

        this.scaleY = this.drawHeight / totalThickness;
    },

    /**
     * Draw background grid
     */
    drawGrid: function () {

        const ctx = this.ctx;

        ctx.strokeStyle = '#dddddd';
        ctx.lineWidth = 1;

        /**
         * Vertical thickness labels
         * 0, 60, 120, 180
         */

        const yMarks = [0, 60, 120, 180];

        yMarks.forEach(value => {

            const y =
                this.startY +
                this.drawHeight -
                (value * this.scaleY);

            // line
            ctx.beginPath();
            ctx.moveTo(this.startX, y);
            ctx.lineTo(this.startX + this.drawWidth, y);
            ctx.stroke();

            // label
            ctx.fillStyle = '#666';
            ctx.font = '16px Arial';

            ctx.fillText(
                value,
                this.startX - 40,
                y + 5
            );
        });

        /**
         * Horizontal direction labels
         * 0 - 150
         */

        for (let i = 0; i <= this.length; i += 30) {

            const x =
                this.startX +
                (i / this.length) * this.drawWidth;

            // tick
            ctx.beginPath();
            ctx.moveTo(x, this.startY + this.drawHeight);
            ctx.lineTo(x, this.startY + this.drawHeight + 8);
            ctx.stroke();

            // label
            ctx.save();

            ctx.translate(
                x - 5,
                this.startY + this.drawHeight + 35
            );

            ctx.rotate(-Math.PI / 4);

            ctx.fillStyle = '#666';
            ctx.font = '16px Arial';

            ctx.fillText(i, 0, 0);

            ctx.restore();
        }
    },

    /**
     * Draw all layers
     */
    drawLayers: function () {

        let currentY = this.startY;

        this.layers.forEach(layer => {

            const height =
                layer.thickness * this.scaleY;

            this.drawLayer(
                layer,
                currentY,
                height
            );

            currentY += height;
        });
    },

    /**
     * Draw single layer
     */
    drawLayer: function (layer, y, height) {

        const ctx = this.ctx;

        // wood base
        ctx.fillStyle = '#d8bf8b';

        ctx.fillRect(
            this.startX,
            y,
            this.drawWidth,
            height
        );

        // border
        ctx.strokeStyle = '#8BC34A';
        ctx.lineWidth = 2;

        ctx.strokeRect(
            this.startX,
            y,
            this.drawWidth,
            height
        );

        // pattern
        if (layer.angle === 0) {

            this.drawHorizontalPattern(y, height);

        } else {

            this.drawVerticalPattern(y, height);
        }

        // label
        ctx.fillStyle = '#666';
        ctx.font = '18px Arial';

        ctx.fillText(
            `${layer.label.toLowerCase()}: ${layer.thickness}mm ${layer.grade}`,
            this.startX + this.drawWidth + 25,
            y + (height / 2)
        );
    },

    /**
     * Horizontal grain
     */
    drawHorizontalPattern: function (y, height) {

        const ctx = this.ctx;

        ctx.strokeStyle = '#8d6e63';
        ctx.lineWidth = 1;

        for (let lineY = y + 6; lineY < y + height; lineY += 8) {

            ctx.beginPath();

            for (
                let x = this.startX;
                x <= this.startX + this.drawWidth;
                x += 10
            ) {

                const wave =
                    Math.sin(x * 0.02) * 2;

                if (x === this.startX) {

                    ctx.moveTo(x, lineY + wave);

                } else {

                    ctx.lineTo(x, lineY + wave);
                }
            }

            ctx.stroke();
        }
    },

    /**
     * Vertical/end grain pattern
     */
    drawVerticalPattern: function (y, height) {

        const ctx = this.ctx;

        ctx.strokeStyle = '#b59b6a';
        ctx.lineWidth = 1;

        const blockWidth = 110;

        for (
            let x = this.startX;
            x < this.startX + this.drawWidth;
            x += blockWidth
        ) {

            for (let r = 10; r < 80; r += 8) {

                ctx.beginPath();

                ctx.arc(
                    x + blockWidth / 2,
                    y + height,
                    r,
                    Math.PI,
                    2 * Math.PI
                );

                ctx.stroke();
            }

            // center separator
            ctx.beginPath();

            ctx.moveTo(
                x + blockWidth / 2,
                y
            );

            ctx.lineTo(
                x + blockWidth / 2,
                y + height
            );

            ctx.stroke();
        }
    },

    /**
     * Axis titles
     */
    drawAxisLabels: function () {

        const ctx = this.ctx;

        // Y title
        ctx.save();

        ctx.translate(
            25,
            this.startY + (this.drawHeight / 2)
        );

        ctx.rotate(-Math.PI / 2);

        ctx.fillStyle = '#555';
        ctx.font = '22px Arial';

        ctx.fillText(
            'Slab Thickness (mm)',
            0,
            0
        );

        ctx.restore();

        // X title
        ctx.fillStyle = '#555';
        ctx.font = '22px Arial';

        ctx.fillText(
            'Primary Direction',
            this.startX + 300,
            this.startY + this.drawHeight + 80
        );
    }
};