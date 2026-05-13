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

    getDeflectionEquation(beam, load) {

        return function (x) {

            // convert span from meter -> mm
            const L = beam.primarySpan * 1000;

            // convert x from meter -> mm
            const xx = x * 1000;

            // stiffness
            const EI = beam.material.properties.EI;

            // load
            const w = load;

            // deflection equation
            const y =
                (
                    w * xx *
                    (
                        Math.pow(L, 3)
                        - (2 * L * Math.pow(xx, 2))
                        + Math.pow(xx, 3)
                    )
                // ) / (24 * EI);
                ) / (30 * EI);

            return {
                x: x,
                y: -y
            };
        };
    }

    getBendingMomentEquation(beam, load) {

        return function (x) {

            const L = beam.primarySpan;
            const w = load;

            const y =
                ((w * L * x) / 2)
                - ((w * Math.pow(x, 2)) / 2);

            return {
                x: x,
                y: y
            };
        };
    }

    getShearForceEquation(beam, load) {

        return function (x) {

            const L = beam.primarySpan;
            const w = load;

            const y =
                ((w * L) / 2)
                - (w * x);

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

    getDeflectionEquation(beam, load) {

        return function (x) {

            const L1 = beam.primarySpan;
            const L2 = beam.secondarySpan;
            const total = L1 + L2;

            let y;

            if (x <= L1) {

                y =
                    -0.5 *
                    Math.sin((Math.PI * x) / L1);

            } else {

                const xx = x - L1;

                y =
                    -1 *
                    Math.sin((Math.PI * xx) / L2);
            }

            return {
                x: x,
                y: y
            };
        };
    }

    getBendingMomentEquation(beam, load) {

        return function (x) {

            const L1 = beam.primarySpan;
            const L2 = beam.secondarySpan;

            let y;

            if (x <= L1) {

                y =
                    (load * x * (L1 - x)) / 2;

            } else {

                const xx = x - L1;

                y =
                    (load * xx * (L2 - xx)) / 2;
            }

            return {
                x: x,
                y: y
            };
        };
    }

    getShearForceEquation(beam, load) {

        return function (x) {

            const L1 = beam.primarySpan;
            const L2 = beam.secondarySpan;

            let y;

            if (x <= L1) {

                y =
                    ((load * L1) / 2)
                    - (load * x);

            } else {

                const xx = x - L1;

                y =
                    ((load * L2) / 2)
                    - (load * xx);
            }

            return {
                x: x,
                y: y
            };
        };
    }
};
