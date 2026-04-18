'use strict';

/** ============================ Beam Analysis Data Type ============================ */

/**
 * Beam material specification.
 *
 * @param {String} name         Material name
 * @param {Object} properties   Material properties {EI : 0, GA : 0, ....}
 */
class Material {
    constructor(name, properties) {
        this.name = name;
        this.properties = properties;
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




/** ============================ Beam Analysis Analyzer ============================ */

/**
 * Available analyzers for different conditions
 */
BeamAnalysis.analyzer = {};

/**
 * Calculate deflection, bending stress and shear stress for a simply supported beam
 *
 * @param {Beam}   beam   The beam object
 * @param {Number}  load    The applied load
 */
BeamAnalysis.analyzer.simplySupported = class {
    constructor(beam, load) {
        this.beam = beam;
        this.load = load;
    }
    getDeflectionEquation(beam, load) {
        return function (x) {
            let L = beam.primarySpan;
            let w = load;
            let EI = beam.material.properties.EI / 1000000000;
            let j2 = beam.j2;
            let y = -(w * x / (24 * EI)) * (Math.pow(L, 3) - 2 * L * Math.pow(x, 2) + Math.pow(x, 3)) * j2 * 1000;
            return {
                x: x,
                y: y
            };
        };
    }
    getBendingMomentEquation(beam, load) {
        return function (x) {
            let L = beam.primarySpan;
            let w = load;
            let y = -(w * x / 2) * (L - x);
            return {
                x: x,
                y: y
            };
        };
    }
    getShearForceEquation(beam, load) {
        return function (x) {
            let L = beam.primarySpan;
            let w = load;
            let y = w * (L / 2 - x);
            return {
                x: x,
                y: y
            };
        };
    }
};


/**
 * Calculate deflection, bending stress and shear stress for a beam with two spans of equal condition
 *
 * @param {Beam}   beam   The beam object
 * @param {Number}  load    The applied load
 */
BeamAnalysis.analyzer.twoSpanUnequal = class {
    constructor(beam, load) {
        this.beam = beam;
        this.load = load;
    }
    getDeflectionEquation(beam, load) {
        return function (x) {
            let L1 = beam.primarySpan;
            let L2 = beam.secondarySpan;
            let L = L1 + L2;
            let w = load;
            let EI = beam.material.properties.EI / 1000000000;
            let j2 = beam.j2;

            let M1 = -(w * Math.pow(L2, 3) + w * Math.pow(L1, 3)) / (8 * L);
            let R1 = (M1 / L1) + (w * L1 / 2);
            let R3 = (M1 / L2) + (w * L2 / 2);
            let R2 = w * L1 + w * L2 - R1 - R3;

            let y = 0;
            if (x <= L1) {
                y = (x / (24 * EI)) * (4 * R1 * Math.pow(x, 2) - w * Math.pow(x, 3) + w * Math.pow(L1, 3) - 4 * R1 * Math.pow(L1, 2)) * 1000 * j2;
            } else {
                let num = (R1 * x / 6) * (Math.pow(x, 2) - Math.pow(L1, 2))
                          + (R2 * x / 6) * (Math.pow(x, 2) - 3 * L1 * x + 3 * Math.pow(L1, 2))
                          - (R2 * Math.pow(L1, 3) / 6)
                          - (w * x / 24) * (Math.pow(x, 3) - Math.pow(L1, 3));
                y = (num / EI) * 1000 * j2;
            }

            return {
                x: x,
                y: y
            };
        };
    }
    getBendingMomentEquation(beam, load) {
        return function (x) {
            let L1 = beam.primarySpan;
            let L2 = beam.secondarySpan;
            let L = L1 + L2;
            let w = load;

            let M1 = -(w * Math.pow(L2, 3) + w * Math.pow(L1, 3)) / (8 * L);
            let R1 = (M1 / L1) + (w * L1 / 2);
            let R3 = (M1 / L2) + (w * L2 / 2);
            let R2 = w * L1 + w * L2 - R1 - R3;

            let y = 0;
            if (x <= L1) {
                y = -(R1 * x - 0.5 * w * Math.pow(x, 2));
            } else {
                y = -((R1 * x + R2 * (x - L1)) - (0.5 * w * Math.pow(x, 2)));
            }
            return {
                x: x,
                y: y
            };
        };
    }
    getShearForceEquation(beam, load) {
        return function (x) {
            let L1 = beam.primarySpan;
            let L2 = beam.secondarySpan;
            let L = L1 + L2;
            let w = load;

            let M1 = -(w * Math.pow(L2, 3) + w * Math.pow(L1, 3)) / (8 * L);
            let R1 = (M1 / L1) + (w * L1 / 2);
            let R3 = (M1 / L2) + (w * L2 / 2);
            let R2 = w * L1 + w * L2 - R1 - R3;

            // Note: Boundary check at L1
            let y = 0;
            if (x < L1) {
                y = R1 - w * x;
            } else {
                y = R1 + R2 - w * x;
            }
            return {
                x: x,
                y: y
            };
        };
    }
};
