'use strict';

/** ============================ Beam Analysis Data Type ============================ */

/**
 * Beam material specification.
 *
 * @param {String} name         Material name
 * @param {Object} properties   Material properties {EI : 0, j2 : 1, ....}
 */
class Material {
    constructor(name, properties) {
        this.name = name;
        this.properties = properties || {};
    }
}

/**
 *
 * @param {Number} primarySpan          Beam primary span length
 * @param {Number} secondarySpan        Beam secondary span length
 * @param {Material} material           Beam material object
 */
class Beam {
    constructor(primarySpan, secondarySpan, material) {
        this.primarySpan = primarySpan;
        this.secondarySpan = secondarySpan;
        this.material = material;
    }
}

/** ============================ Beam Analysis Class ============================ */

class BeamAnalysis {
    constructor() {
        this.options = {
            condition: 'simply-supported'
        };

        this.analyzer = {
            'simply-supported': new BeamAnalysis.analyzer.simplySupported(),
            'two-span-unequal': new BeamAnalysis.analyzer.twoSpanUnequal()
        };
    }
    /**
     *
     * @param {Beam} beam
     * @param {Number} load
     */
    getDeflection(beam, load, condition) {
        var analyzer = this.analyzer[condition];

        if (analyzer) {
            return {
                beam: beam,
                load: load,
                equation: analyzer.getDeflectionEquation(beam, load)
            };
        } else {
            throw new Error('Invalid condition');
        }
    }
    getBendingMoment(beam, load, condition) {
        var analyzer = this.analyzer[condition];

        if (analyzer) {
            return {
                beam: beam,
                load: load,
                equation: analyzer.getBendingMomentEquation(beam, load)
            };
        } else {
            throw new Error('Invalid condition');
        }
    }
    getShearForce(beam, load, condition) {
        var analyzer = this.analyzer[condition];

        if (analyzer) {
            return {
                beam: beam,
                load: load,
                equation: analyzer.getShearForceEquation(beam, load)
            };
        } else {
            throw new Error('Invalid condition');
        }
    }
}

/** ============================ Helpers (excel/beam-analysis.xlsx) ============================ */

function eiKnM2(eiNmm2) {
    return eiNmm2 / Math.pow(1000, 3);
}

function getJ2(beam) {
    var j = beam && beam.material && beam.material.properties && beam.material.properties.j2;
    if (j === undefined || j === null || isNaN(j)) {
        return 1;
    }
    return j;
}

/** ============================ Beam Analysis Analyzer ============================ */

/**
 * Available analyzers for different conditions
 */
BeamAnalysis.analyzer = {};

/**
 * Simply supported beam, UDL w (kN/m). Span = primarySpan only.
 */
BeamAnalysis.analyzer.simplySupported = class {
    constructor() {}

    getDeflectionEquation(beam, load) {
        var w = load;
        var L = beam.primarySpan;
        var EI = beam.material.properties.EI;
        var j2 = getJ2(beam);
        var eiAdj = eiKnM2(EI);

        return function (x) {
            var xm = x;
            var poly = Math.pow(L, 3) - 2 * L * xm * xm + Math.pow(xm, 3);
            var deltaMm = (-w * xm) / (24 * eiAdj) * poly * j2 * 1000;
            return { x: xm, y: deltaMm };
        };
    }

    getBendingMomentEquation(beam, load) {
        var w = load;
        var L = beam.primarySpan;

        return function (x) {
            var xm = x;
            var M = (w * xm * (L - xm)) / 2;
            return { x: xm, y: M };
        };
    }

    getShearForceEquation(beam, load) {
        var w = load;
        var L = beam.primarySpan;
        var R = (w * L) / 2;

        return function (x) {
            var xm = x;
            var V = R - w * xm;
            return { x: xm, y: V };
        };
    }
};

/**
 * Two-span continuous beam, UDL on both spans. L1 = primarySpan, L2 = secondarySpan.
 */
BeamAnalysis.analyzer.twoSpanUnequal = class {
    constructor() {}

    _reactions(w, L1, L2) {
        var L = L1 + L2;
        var M1 = -((w * Math.pow(L1, 3)) + (w * Math.pow(L2, 3))) / (8 * L);
        var R1 = M1 / L1 + (w * L1) / 2;
        var R3 = M1 / L2 + (w * L2) / 2;
        var R2 = w * L1 + w * L2 - R1 - R3;
        return { M1: M1, R1: R1, R2: R2, R3: R3, L1: L1, L2: L2, L: L };
    }

    getDeflectionEquation(beam, load) {
        var w = load;
        var L1 = beam.primarySpan;
        var L2 = beam.secondarySpan;
        var EI = beam.material.properties.EI;
        var j2 = getJ2(beam);
        var eiAdj = eiKnM2(EI);
        var rx = this._reactions(w, L1, L2);
        var R1 = rx.R1;
        var R2 = rx.R2;

        return function (x) {
            var xm = x;
            var deltaMm;

            if (xm <= L1) {
                var inner =
                    4 * R1 * xm * xm -
                    w * Math.pow(xm, 3) +
                    w * Math.pow(L1, 3) -
                    4 * R1 * L1 * L1;
                deltaMm = (xm / (24 * eiAdj)) * inner * 1000 * j2;
            } else {
                var t1 = (R1 * xm) / 6 * (xm * xm - L1 * L1);
                var t2 =
                    (R2 * xm) / 6 *
                    (xm * xm - 3 * L1 * xm + 3 * L1 * L1);
                var t3 = (R2 * Math.pow(L1, 3)) / 6;
                var t4 = ((w * xm) / 24) * (Math.pow(xm, 3) - Math.pow(L1, 3));
                deltaMm = ((t1 + t2 - t3 - t4) / eiAdj) * 1000 * j2;
            }

            return { x: xm, y: deltaMm };
        };
    }

    getBendingMomentEquation(beam, load) {
        var w = load;
        var L1 = beam.primarySpan;
        var rx = this._reactions(w, L1, beam.secondarySpan);
        var R1 = rx.R1;
        var R2 = rx.R2;

        return function (x) {
            var xm = x;
            var M;
            if (xm <= L1) {
                M = R1 * xm - 0.5 * w * xm * xm;
            } else {
                M = R1 * xm + R2 * (xm - L1) - 0.5 * w * xm * xm;
            }
            return { x: xm, y: M };
        };
    }

    getShearForceEquation(beam, load) {
        var w = load;
        var L1 = beam.primarySpan;
        var rx = this._reactions(w, L1, beam.secondarySpan);
        var R1 = rx.R1;
        var R2 = rx.R2;

        return function (x) {
            var xm = x;
            var V;
            if (xm < L1) {
                V = R1 - w * xm;
            } else if (xm > L1) {
                V = R1 + R2 - w * xm;
            } else {
                V = R1 - w * L1;
            }
            return { x: xm, y: V };
        };
    }
};
