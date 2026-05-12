'use strict';

/** ============================ Beam Analysis Data Type ============================ */

/**
 * Beam material specification.
 * @param {String} name         Material name
 * @param {Object} properties   Material properties {EI : 0, j2: 1, ....}
 */
class Material {
    constructor(name, properties) {
        this.name = name;
        this.properties = properties;
    }
}

/**
 * @param {Number} primarySpan          Beam primary span length (L1)
 * @param {Number} secondarySpan        Beam secondary span length (L2)
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

    getDeflection(beam, load, condition) {
        var analyzer = this.analyzer[condition];
        if (analyzer) {
            return { beam: beam, load: load, equation: analyzer.getDeflectionEquation(beam, load) };
        } else {
            throw new Error('Invalid condition');
        }
    }

    getBendingMoment(beam, load, condition) {
        var analyzer = this.analyzer[condition];
        if (analyzer) {
            return { beam: beam, load: load, equation: analyzer.getBendingMomentEquation(beam, load) };
        } else {
            throw new Error('Invalid condition');
        }
    }

    getShearForce(beam, load, condition) {
        var analyzer = this.analyzer[condition];
        if (analyzer) {
            return { beam: beam, load: load, equation: analyzer.getShearForceEquation(beam, load) };
        } else {
            throw new Error('Invalid condition');
        }
    }
}

/** ============================ Beam Analysis Analyzer ============================ */

BeamAnalysis.analyzer = {};

/**
 * Simply Supported Beam with Uniformly Distributed Load (UDL)
 *
 * Formulas (from Excel sheet "1. Simply Supported UDL"):
 *   Shear Force:    V(x) = w * (L/2 - x)                            [kN]
 *   Bending Moment: M(x) = w*x/2 * (L - x)                         [kN·m] (sagging positive)
 *   Deflection:     δ(x) = -w*x/(24*EI) * (L³ - 2*L*x² + x³)      [mm]
 *                   EI in N·mm² converted to kN·m² by ÷1e9
 *                   Result multiplied by j2*1000 to get mm
 */
BeamAnalysis.analyzer.simplySupported = class {
    constructor() { }

    getShearForceEquation(beam, load) {
        var w = load;
        var L = beam.primarySpan;
        return function (x) {
            return { x: x, y: w * (L / 2 - x) };
        };
    }

    getBendingMomentEquation(beam, load) {
        var w = load;
        var L = beam.primarySpan;
        return function (x) {
            return { x: x, y: w * x / 2 * (L - x) };
        };
    }

    getDeflectionEquation(beam, load) {
        var w = load;
        var L = beam.primarySpan;
        var EI_kNm2 = beam.material.properties.EI / 1e9;
        var j2 = beam.material.properties.j2 || 1;
        return function (x) {
            var delta = -w * x / (24 * EI_kNm2) * (Math.pow(L, 3) - 2 * L * x * x + Math.pow(x, 3)) * j2 * 1000;
            return { x: x, y: delta };
        };
    }
};


/**
 * Two-Span Continuous Beam with Unequal Spans and Uniformly Distributed Load (UDL)
 *
 * Reactions (from Excel sheet "2. Two unequal Span Equal UDL"):
 *   M1  = -(w*L2³ + w*L1³) / (8*(L1+L2))     [kN·m]  (hogging moment at middle support)
 *   R1  =  M1/L1 + w*L1/2                     [kN]
 *   R3  =  M1/L2 + w*L2/2                     [kN]
 *   R2  =  w*(L1+L2) - R1 - R3               [kN]
 *
 * Shear Force (x from left end):
 *   Span 1 (0 ≤ x ≤ L1):  V(x) = R1 - w*x
 *   Span 2 (L1 < x ≤ L):  V(x) = R1 + R2 - w*x
 *
 * Bending Moment:
 *   Span 1 (0 ≤ x ≤ L1):  M(x) = -(R1*x - w*x²/2)
 *   Span 2 (L1 < x ≤ L):  M(x) = -(R1*x + R2*(x-L1) - w*x²/2)
 *
 * Deflection:
 *   Span 1 (0 ≤ x ≤ L1):
 *     δ(x) = [x/(24*EI)] * (4*R1*x² - w*x³ + w*L1³ - 4*R1*L1²) * j2 * 1000
 *   Span 2 (L1 < x ≤ L):
 *     δ(x) = [(R1*x/6)*(x²-L1²) + (R2*x/6)*(x²-3*L1*x+3*L1²)
 *             - R2*L1³/6 - (w*x/24)*(x³-L1³)] / EI * j2 * 1000
 */
BeamAnalysis.analyzer.twoSpanUnequal = class {
    constructor() { }

    _getReactions(beam, load) {
        var w = load;
        var L1 = beam.primarySpan;
        var L2 = beam.secondarySpan;
        var M1 = -(w * Math.pow(L2, 3) + w * Math.pow(L1, 3)) / (8 * (L1 + L2));
        var R1 = (M1 / L1) + (w * L1 / 2);
        var R3 = (M1 / L2) + (w * L2 / 2);
        var R2 = w * L1 + w * L2 - R1 - R3;
        return { M1: M1, R1: R1, R2: R2, R3: R3 };
    }

    getShearForceEquation(beam, load) {
        var w = load;
        var L1 = beam.primarySpan;
        var reactions = this._getReactions(beam, load);
        var R1 = reactions.R1;
        var R2 = reactions.R2;
        return function (x) {
            var V = (x <= L1) ? R1 - w * x : R1 + R2 - w * x;
            return { x: x, y: V };
        };
    }

    getBendingMomentEquation(beam, load) {
        var w = load;
        var L1 = beam.primarySpan;
        var L2 = beam.secondarySpan;
        var L = L1 + L2;
        var reactions = this._getReactions(beam, load);
        var R1 = reactions.R1;
        var R2 = reactions.R2;
        return function (x) {
            var M;
            if (x === 0 || x === L) {
                M = 0;
            } else if (x < L1) {
                M = -(R1 * x - 0.5 * w * x * x);
            } else if (x === L1) {
                M = -(R1 * L1 - 0.5 * w * L1 * L1);
            } else {
                M = -(R1 * x + R2 * (x - L1) - 0.5 * w * x * x);
            }
            return { x: x, y: M };
        };
    }

    getDeflectionEquation(beam, load) {
        var w = load;
        var L1 = beam.primarySpan;
        var EI_kNm2 = beam.material.properties.EI / 1e9;
        var j2 = beam.material.properties.j2 || 1;
        var reactions = this._getReactions(beam, load);
        var R1 = reactions.R1;
        var R2 = reactions.R2;
        return function (x) {
            var delta;
            if (x <= L1) {
                delta = (x / (24 * EI_kNm2)) *
                    (4 * R1 * x * x - w * Math.pow(x, 3) + w * Math.pow(L1, 3) - 4 * R1 * L1 * L1) *
                    j2 * 1000;
            } else {
                delta = (
                    (R1 * x / 6) * (x * x - L1 * L1) +
                    (R2 * x / 6) * (x * x - 3 * L1 * x + 3 * L1 * L1) -
                    (R2 * Math.pow(L1, 3) / 6) -
                    (w * x / 24) * (Math.pow(x, 3) - Math.pow(L1, 3))
                ) / EI_kNm2 * j2 * 1000;
            }
            return { x: x, y: delta };
        };
    }
};