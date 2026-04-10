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
        var span = beam.primarySpan;
        var EI = beam.material.properties.EI;

        return function (x) {
            return {
                x: x,
                y: (-load * x * (Math.pow(span, 3) - (2 * span * Math.pow(x, 2)) + Math.pow(x, 3))) / (24 * (EI / Math.pow(1000, 3))) * 1000
            };
        };
    }
    getBendingMomentEquation(beam, load) {
        var span = beam.primarySpan;

        return function (x) {
            return {
                x: x,
                y: (load * x * (span - x)) / 2
            };
        };
    }
    getShearForceEquation(beam, load) {
        var span = beam.primarySpan;

        return function (x) {
            return {
                x: x,
                y: load * ((span / 2) - x)
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
        var l1 = beam.primarySpan;
        var EI = beam.material.properties.EI;
        var reaction = this.getReactions(beam, load);
        var r1 = reaction.r1;
        var r2 = reaction.r2;

        return function (x) {
            var y;

            if (x <= l1) {
                y = (
                    (x / (24 * (EI / Math.pow(1000, 3)))) *
                    (
                        (4 * r1 * Math.pow(x, 2)) -
                        (load * Math.pow(x, 3)) +
                        (load * Math.pow(l1, 3)) -
                        (4 * r1 * Math.pow(l1, 2))
                    )
                ) * 1000;
            } else {
                y = (
                    (
                        ((r1 * x / 6) * (Math.pow(x, 2) - Math.pow(l1, 2))) +
                        ((r2 * x / 6) * (Math.pow(x, 2) - (3 * l1 * x) + (3 * Math.pow(l1, 2)))) -
                        (r2 * Math.pow(l1, 3) / 6) -
                        ((load * x / 24) * (Math.pow(x, 3) - Math.pow(l1, 3)))
                    ) / (EI / Math.pow(1000, 3))
                ) * 1000;
            }

            return {
                x: x,
                y: y
            };
        };
    }
    getBendingMomentEquation(beam, load) {
        var l1 = beam.primarySpan;
        var reaction = this.getReactions(beam, load);
        var r1 = reaction.r1;
        var r2 = reaction.r2;

        return function (x) {
            var y;

            if (x <= l1) {
                y = (r1 * x) - (0.5 * load * Math.pow(x, 2));
            } else {
                y = ((r1 * x) + (r2 * (x - l1))) - (0.5 * load * Math.pow(x, 2));
            }

            return {
                x: x,
                y: y
            };
        };
    }
    getShearForceEquation(beam, load) {
        var l1 = beam.primarySpan;
        var reaction = this.getReactions(beam, load);
        var r1 = reaction.r1;
        var r2 = reaction.r2;
        var eq = function (x) {
            var y;

            if (x < l1) {
                y = r1 - (load * x);
            } else {
                y = (r1 + r2) - (load * x);
            }

            return {
                x: x,
                y: y
            };
        };

        eq.discontinuities = [l1];

        return eq;
    }
    getReactions(beam, load) {
        var l1 = beam.primarySpan;
        var l2 = beam.secondarySpan;
        var m1 = -((load * Math.pow(l2, 3)) + (load * Math.pow(l1, 3))) / (8 * (l1 + l2));
        var r1 = (m1 / l1) + ((load * l1) / 2);
        var r3 = (m1 / l2) + ((load * l2) / 2);
        var r2 = (load * l1) + (load * l2) - r1 - r3;

        return {
            m1: m1,
            r1: r1,
            r2: r2,
            r3: r3
        };
    }
};
