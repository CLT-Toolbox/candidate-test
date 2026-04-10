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
        var L = Number(beam.primarySpan);
        var w = Number(load);
        var EI = Number(beam.material && beam.material.properties && beam.material.properties.EI);
        var j2 = Number(beam.material && beam.material.properties && beam.material.properties.j2);
        var loadFactor = Number.isFinite(j2) ? j2 : 1;
        var effectiveEI = EI / Math.pow(1000, 3); // N-mm2 -> kN-m2

        return function (x) {
            var X = Number(x);
            var y = null;

            if (
                Number.isFinite(X) &&
                Number.isFinite(L) &&
                Number.isFinite(w) &&
                Number.isFinite(effectiveEI) &&
                effectiveEI !== 0 &&
                X >= 0 &&
                X <= L
            ) {
                y = -(
                    (w * X) /
                    (24 * effectiveEI) *
                    (Math.pow(L, 3) - 2 * L * Math.pow(X, 2) + Math.pow(X, 3))
                ) * loadFactor * 1000; // m -> mm
            }

            return {
                x: X,
                y: y
            };
        };
    }
    getBendingMomentEquation(beam, load) {
        var L = Number(beam.primarySpan);
        var w = Number(load);

        return function (x) {
            var X = Number(x);
            var y = null;

            if (
                Number.isFinite(X) &&
                Number.isFinite(L) &&
                Number.isFinite(w) &&
                X >= 0 &&
                X <= L
            ) {
                y = (w * X * (L - X)) / 2;
            }

            return {
                x: X,
                y: y
            };
        };
    }
    getShearForceEquation(beam, load) {
        var L = Number(beam.primarySpan);
        var w = Number(load);

        return function (x) {
            var X = Number(x);
            var y = null;

            if (
                Number.isFinite(X) &&
                Number.isFinite(L) &&
                Number.isFinite(w) &&
                X >= 0 &&
                X <= L
            ) {
                y = w * (L / 2 - X);
            }

            return {
                x: X,
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
        var L1 = Number(beam.primarySpan);
        var L2 = Number(beam.secondarySpan);
        var w = Number(load);
        var EI = Number(beam.material && beam.material.properties && beam.material.properties.EI);
        var j2 = Number(beam.material && beam.material.properties && beam.material.properties.j2);
        var loadFactor = Number.isFinite(j2) ? j2 : 1;
        var totalLength = L1 + L2;
        var effectiveEI = EI / Math.pow(1000, 3); // N-mm2 -> kN-m2

        var supportMoment = -((w * Math.pow(L2, 3)) + (w * Math.pow(L1, 3))) / (8 * (L1 + L2));
        var leftReaction = (supportMoment / L1) + ((w * L1) / 2);
        var rightReaction = (supportMoment / L2) + ((w * L2) / 2);
        var middleReaction = (w * L1) + (w * L2) - leftReaction - rightReaction;

        return function (x) {
            var X = Number(x);
            var y = null;

            if (
                Number.isFinite(X) &&
                Number.isFinite(L1) &&
                Number.isFinite(L2) &&
                Number.isFinite(w) &&
                Number.isFinite(effectiveEI) &&
                effectiveEI !== 0 &&
                Number.isFinite(leftReaction) &&
                Number.isFinite(middleReaction) &&
                X >= 0 &&
                X <= totalLength
            ) {
                if (X <= L1) {
                    y = (
                        (X / (24 * effectiveEI)) *
                        (
                            (4 * leftReaction * Math.pow(X, 2)) -
                            (w * Math.pow(X, 3)) +
                            (w * Math.pow(L1, 3)) -
                            (4 * leftReaction * Math.pow(L1, 2))
                        )
                    ) * 1000 * loadFactor;
                } else {
                    y = (
                        (
                            ((leftReaction * X) / 6) * (Math.pow(X, 2) - Math.pow(L1, 2)) +
                            ((middleReaction * X) / 6) *
                                (
                                    Math.pow(X, 2) -
                                    (3 * L1 * X) +
                                    (3 * Math.pow(L1, 2))
                                ) -
                            (middleReaction * Math.pow(L1, 3)) / 6 -
                            ((w * X) / 24) * (Math.pow(X, 3) - Math.pow(L1, 3))
                        ) / effectiveEI
                    ) * 1000 * loadFactor;
                }
            }

            return {
                x: X,
                y: y
            };
        };
    }
    getBendingMomentEquation(beam, load) {
        var L1 = Number(beam.primarySpan);
        var L2 = Number(beam.secondarySpan);
        var w = Number(load);
        var totalLength = L1 + L2;
        var supportMoment = -((w * Math.pow(L2, 3)) + (w * Math.pow(L1, 3))) / (8 * (L1 + L2));
        var leftReaction = (supportMoment / L1) + ((w * L1) / 2);
        var rightReaction = (supportMoment / L2) + ((w * L2) / 2);
        var middleReaction = (w * L1) + (w * L2) - leftReaction - rightReaction;

        return function (x) {
            var X = Number(x);
            var y = null;

            if (
                Number.isFinite(X) &&
                Number.isFinite(L1) &&
                Number.isFinite(L2) &&
                Number.isFinite(w) &&
                Number.isFinite(leftReaction) &&
                Number.isFinite(middleReaction) &&
                X >= 0 &&
                X <= totalLength
            ) {
                if (X <= L1) {
                    y = (leftReaction * X) - (w * Math.pow(X, 2) / 2);
                } else {
                    y = (leftReaction * X) + (middleReaction * (X - L1)) - (w * Math.pow(X, 2) / 2);
                }
            }

            return {
                x: X,
                y: y
            };
        };
    }
    getShearForceEquation(beam, load) {
        var L1 = Number(beam.primarySpan);
        var L2 = Number(beam.secondarySpan);
        var w = Number(load);
        var totalLength = L1 + L2;
        var supportMoment = -((w * Math.pow(L2, 3)) + (w * Math.pow(L1, 3))) / (8 * (L1 + L2));
        var leftReaction = (supportMoment / L1) + ((w * L1) / 2);
        var rightReaction = (supportMoment / L2) + ((w * L2) / 2);
        var middleReaction = (w * L1) + (w * L2) - leftReaction - rightReaction;

        return function (x) {
            var X = Number(x);
            var y = null;

            if (
                Number.isFinite(X) &&
                Number.isFinite(L1) &&
                Number.isFinite(L2) &&
                Number.isFinite(w) &&
                Number.isFinite(leftReaction) &&
                Number.isFinite(middleReaction) &&
                X >= 0 &&
                X <= totalLength
            ) {
                if (X < L1) {
                    y = leftReaction - (w * X);
                } else {
                    y = leftReaction + middleReaction - (w * X);
                }
            }

            return {
                x: X,
                y: y
            };
        };
    }
};
