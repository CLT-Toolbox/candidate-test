"use strict";

function LayupDrawer() {
  /**
   * Canvas element
   */
  this.canvas = null;
  this.ctx = null;
  this.padding = { top: 30, right: 130, bottom: 55, left: 65 };
}

LayupDrawer.prototype = {
  /**
   * Configure the canvas
   *
   * @param {HTMLCanvasElement} canvas  Canvas element
   */
  init: function (canvas) {
    this.canvas = canvas;
    this.ctx = canvas.getContext("2d");
  },

  /**
   * Draw a layup configuration on the canvas
   *
   * @param {Object} layup   Layup object (t1, t2, ... keys)
   * @param {Number} length  Layup length in mm (default 150)
   */
  drawLayup: function (layup, length) {
    var self = this;
    length = length || 150;

    var layers = Object.keys(layup).map(function (k) {
      return layup[k];
    });
    var totalThickness = layers.reduce(function (sum, l) {
      return sum + l.thickness;
    }, 0);

    // Load both grain images, then render
    var images = {};
    var needed = {
      0: "images/paralel-grain-0.jpg",
      90: "images/perpendicular-grain-90.jpg",
    };
    var keys = Object.keys(needed);
    var loaded = 0;

    keys.forEach(function (angle) {
      var img = new Image();
      img.onload = function () {
        images[parseInt(angle)] = img;
        loaded++;
        if (loaded === keys.length) {
          self._render(layers, images, length, totalThickness);
        }
      };
      img.onerror = function () {
        loaded++;
        if (loaded === keys.length) {
          self._render(layers, images, length, totalThickness);
        }
      };
      img.src = needed[angle];
    });
  },

  /**
   * Main render routine — called once all images have loaded.
   */
  _render: function (layers, images, length, totalThickness) {
    var ctx = this.ctx;
    var canvas = this.canvas;
    var pad = this.padding;

    var drawW = canvas.width - pad.left - pad.right;
    var drawH = canvas.height - pad.top - pad.bottom;

    // ── Background ────────────────────────────────────────────────────────
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // ── Layers (t1 at top, last layer at bottom) ──────────────────────────
    var scaleY = drawH / totalThickness;
    var yOffset = 0; // canvas-y offset from pad.top, increases downward

    var self = this;
    layers.forEach(function (layer) {
      var lh = layer.thickness * scaleY;
      var rx = pad.left;
      var ry = pad.top + yOffset;

      var img = images[layer.angle];
      if (img) {
        var pat = ctx.createPattern(img, "repeat");
        ctx.fillStyle = pat;
      } else {
        ctx.fillStyle = "#e8d5a3";
      }
      ctx.fillRect(rx, ry, drawW, lh);

      yOffset += lh;
    });

    // ── Separator lines (green-yellow) ─────────────────────────────────────
    ctx.strokeStyle = "#a8c840";
    ctx.lineWidth = 2;

    // outer border
    ctx.strokeRect(pad.left, pad.top, drawW, drawH);

    // inner dividers
    var sepY = 0;
    layers.forEach(function (layer, i) {
      if (i > 0) {
        var py = pad.top + sepY;
        ctx.beginPath();
        ctx.moveTo(pad.left, py);
        ctx.lineTo(pad.left + drawW, py);
        ctx.stroke();
      }
      sepY += layer.thickness * scaleY;
    });

    // ── Axes & labels ─────────────────────────────────────────────────────
    this._drawAxes(
      layers,
      images,
      length,
      totalThickness,
      drawW,
      drawH,
      scaleY,
    );
  },

  /**
   * Draw axis ticks, labels, and layer annotations.
   */
  _drawAxes: function (
    layers,
    images,
    length,
    totalThickness,
    drawW,
    drawH,
    scaleY,
  ) {
    var ctx = this.ctx;
    var canvas = this.canvas;
    var pad = this.padding;

    ctx.strokeStyle = "#888";
    ctx.fillStyle = "#333";
    ctx.lineWidth = 1;

    // ── X axis (Primary Direction, 0 → length) ────────────────────────────
    var xStepMm = this._niceStep(length, 6);
    ctx.font = "11px Arial";
    ctx.textAlign = "center";

    for (var xmm = 0; xmm <= length + 0.001; xmm += xStepMm) {
      var px = pad.left + (xmm / length) * drawW;
      ctx.beginPath();
      ctx.moveTo(px, pad.top + drawH);
      ctx.lineTo(px, pad.top + drawH + 5);
      ctx.stroke();
      ctx.fillText(Math.round(xmm), px, pad.top + drawH + 17);
    }

    ctx.font = "12px Arial";
    ctx.fillText("Primary Direction", pad.left + drawW / 2, canvas.height - 8);

    // ── Y axis (Slab Thickness, 0 at bottom → totalThickness at top) ──────
    var yStepMm = this._niceStep(totalThickness, 7);
    ctx.textAlign = "right";
    ctx.font = "11px Arial";

    for (var ymm = 0; ymm <= totalThickness + 0.001; ymm += yStepMm) {
      // canvas-y: 0mm is at the bottom of the draw area
      var py = pad.top + drawH - (ymm / totalThickness) * drawH;
      ctx.beginPath();
      ctx.moveTo(pad.left - 5, py);
      ctx.lineTo(pad.left, py);
      ctx.stroke();
      ctx.fillText(Math.round(ymm), pad.left - 8, py + 4);
    }

    // Y axis title (rotated)
    ctx.save();
    ctx.translate(14, pad.top + drawH / 2);
    ctx.rotate(-Math.PI / 2);
    ctx.textAlign = "center";
    ctx.font = "12px Arial";
    ctx.fillText("Slab Thickness (mm)", 0, 0);
    ctx.restore();

    // ── Right-side layer labels ────────────────────────────────────────────
    ctx.textAlign = "left";
    ctx.font = "11px Arial";
    ctx.fillStyle = "#444";

    var yOff = 0;
    layers.forEach(function (layer) {
      var lh = layer.thickness * scaleY;
      var midY = pad.top + yOff + lh / 2 + 4;
      ctx.fillText(
        layer.label + ": " + layer.thickness + "mm " + layer.grade,
        pad.left + drawW + 8,
        midY,
      );
      yOff += lh;
    });
  },

  /**
   * Return a "nice" round step size that produces roughly `targetCount` ticks
   * for a range of `maxVal`.
   */
  _niceStep: function (maxVal, targetCount) {
    var raw = maxVal / targetCount;
    var mag = Math.pow(10, Math.floor(Math.log(raw) / Math.LN10));
    var norm = raw / mag;
    var nice = norm < 1.5 ? 1 : norm < 3.5 ? 2 : norm < 7.5 ? 5 : 10;
    return nice * mag;
  },
};
