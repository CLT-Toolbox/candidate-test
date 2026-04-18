"use strict";

/**
 * Plot result from the beam analysis calculation into a graph.
 *
 * Requires Chart.js to be loaded before this script.
 * Add to index.html:
 *   <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
 *
 * @param {String} container   ID of the target <canvas> element
 */
class AnalysisPlotter {
  constructor(container) {
    this.container = container;
    this.chart = null;
  }

  /**
   * Sample an equation function into an array of {x, y} points.
   *
   * @param {Function} equation   Function(x) → { x, y }
   * @param {Number}   start      Start of range (m)
   * @param {Number}   end        End of range (m)
   * @param {Number}   steps      Number of sample points (default 200)
   * @returns {Array}  [{x, y}, ...]
   */
  _sample(equation, start, end, steps) {
    steps = steps || 200;
    var points = [];
    for (var i = 0; i <= steps; i++) {
      var x = start + ((end - start) * i) / steps;
      var p = equation(x);
      if (p && p.y !== null) {
        points.push({
          x: parseFloat(p.x.toFixed(6)),
          y: parseFloat(p.y.toFixed(6)),
        });
      }
    }
    return points;
  }

  /**
   * Determine total beam length from the result data object.
   *
   * @param {Object} data   Result from BeamAnalysis.getDeflection / etc.
   * @returns {Number}  Total length in metres
   */
  _totalLength(data) {
    var beam = data.beam;
    var L2 = parseFloat(beam.secondarySpan) || 0;
    return beam.primarySpan + L2;
  }

  /**
   * Infer y-axis label and chart color from the canvas element ID.
   * Expects IDs containing 'deflection', 'shear', or 'bending'.
   *
   * @returns {Object}  { label, color, fill }
   */
  _meta() {
    var id = this.container;
    if (id.indexOf("deflection") !== -1) {
      return {
        label: "Deflection (mm)",
        color: "#3266ad",
        fill: "rgba(50,102,173,0.08)",
      };
    }
    if (id.indexOf("shear") !== -1) {
      return {
        label: "Shear Force (kN)",
        color: "#1D9E75",
        fill: "rgba(29,158,117,0.08)",
      };
    }
    return {
      label: "Bending Moment (kN·m)",
      color: "#D85A30",
      fill: "rgba(216,90,48,0.08)",
    };
  }

  /**
   * Plot equation data onto the canvas.
   *
   * @param {Object} data   Object returned by:
   *                          BeamAnalysis.getDeflection()
   *                          BeamAnalysis.getBendingMoment()
   *                          BeamAnalysis.getShearForce()
   *                        Shape: { beam: Beam, load: Number, equation: Function }
   */
  plot(data) {
    var self = this;

    // Chart.js may not be ready yet — retry until available
    if (typeof Chart === "undefined") {
      setTimeout(function () {
        self.plot(data);
      }, 100);
      return;
    }

    var totalLength = this._totalLength(data);
    var points = this._sample(data.equation, 0, totalLength, 200);
    var meta = this._meta();

    // Destroy previous instance before re-plotting
    if (this.chart) {
      this.chart.destroy();
      this.chart = null;
    }

    var canvas = document.getElementById(this.container);
    if (!canvas) {
      console.error(
        "AnalysisPlotter: canvas #" + this.container + " not found.",
      );
      return;
    }

    this.chart = new Chart(canvas.getContext("2d"), {
      type: "line",
      data: {
        datasets: [
          {
            label: meta.label,
            data: points,
            borderColor: meta.color,
            borderWidth: 2,
            pointRadius: 0,
            tension: 0.3,
            fill: "origin",
            backgroundColor: meta.fill,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        resizeDelay: 200,
        animation: false,

        plugins: {
          legend: {
            display: true,
            position: "top",
          },
          tooltip: {
            callbacks: {
              title: function (items) {
                return "x = " + items[0].parsed.x.toFixed(3) + " m";
              },
              label: function (item) {
                return meta.label + ": " + item.parsed.y.toFixed(4);
              },
            },
          },
        },
        scales: {
          x: {
            type: "linear",
            title: { display: true, text: "Position x (m)" },
            ticks: {
              maxTicksLimit: 10,
              callback: function (v) {
                return v.toFixed(1);
              },
            },
            grid: { color: "rgba(128,128,128,0.1)" },
          },
          y: {
            title: { display: true, text: meta.label },
            ticks: {
              maxTicksLimit: 6,
              callback: function (v) {
                return v.toFixed(2);
              },
            },
            grid: { color: "rgba(128,128,128,0.1)" },
          },
        },
      },
    });
  }
}
