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
                condition: condition,
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
                condition: condition,
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
                condition: condition,
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

const BeamAnalysisHelper = {
    getEI(beam) {
        var EI = beam && beam.material && beam.material.properties
            ? beam.material.properties.EI
            : NaN;

        if (!Number.isFinite(EI) || EI <= 0) {
            throw new Error('Invalid EI value');
        }

        return EI / 1000000000;
    },
    getJ2(beam) {
        var j2 = beam && beam.material && beam.material.properties
            ? beam.material.properties.j2
            : NaN;

        return Number.isFinite(j2) ? j2 : 1;
    },
    isAt(value, target) {
        return Math.abs(value - target) < 1e-9;
    }
};

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
    getState(beam, load) {
        var span = beam.primarySpan;

        if (!Number.isFinite(load)) {
            throw new Error('Invalid load');
        }

        if (!Number.isFinite(span) || span <= 0) {
            throw new Error('Invalid primary span');
        }

        return {
            span: span,
            load: load,
            reaction: (load * span) / 2,
            EI: BeamAnalysisHelper.getEI(beam),
            j2: BeamAnalysisHelper.getJ2(beam)
        };
    }
    getDeflectionEquation(beam, load) {
        var state = this.getState(beam, load);

        return function (x) {
            if (x < 0 || x > state.span) {
                return {
                    x: x,
                    y: null
                };
            }

            return {
                x: x,
                y: (
                    -state.load * x * (
                        Math.pow(state.span, 3) -
                        2 * state.span * Math.pow(x, 2) +
                        Math.pow(x, 3)
                    ) / (24 * state.EI)
                ) * 1000 * state.j2
            };
        };
    }
    getBendingMomentEquation(beam, load) {
        var state = this.getState(beam, load);

        return function (x) {
            if (x < 0 || x > state.span) {
                return {
                    x: x,
                    y: null
                };
            }

            return {
                x: x,
                y: state.reaction * x - (state.load * Math.pow(x, 2)) / 2
            };
        };
    }
    getShearForceEquation(beam, load) {
        var state = this.getState(beam, load);

        return function (x) {
            if (x < 0 || x > state.span) {
                return {
                    x: x,
                    y: null
                };
            }

            return {
                x: x,
                y: state.reaction - (state.load * x)
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
    getState(beam, load) {
        var primarySpan = beam.primarySpan;
        var secondarySpan = beam.secondarySpan;
        var totalSpan = primarySpan + secondarySpan;
        var supportMoment;
        var leftReaction;
        var rightReaction;
        var middleReaction;

        if (!Number.isFinite(load)) {
            throw new Error('Invalid load');
        }

        if (!Number.isFinite(primarySpan) || primarySpan <= 0) {
            throw new Error('Invalid primary span');
        }

        if (!Number.isFinite(secondarySpan) || secondarySpan <= 0) {
            throw new Error('Invalid secondary span');
        }

        supportMoment = -load * (
            Math.pow(primarySpan, 3) + Math.pow(secondarySpan, 3)
        ) / (8 * totalSpan);

        leftReaction = (supportMoment / primarySpan) + ((load * primarySpan) / 2);
        rightReaction = (supportMoment / secondarySpan) + ((load * secondarySpan) / 2);
        middleReaction = (load * totalSpan) - leftReaction - rightReaction;

        return {
            primarySpan: primarySpan,
            secondarySpan: secondarySpan,
            totalSpan: totalSpan,
            load: load,
            EI: BeamAnalysisHelper.getEI(beam),
            j2: BeamAnalysisHelper.getJ2(beam),
            supportMoment: supportMoment,
            leftReaction: leftReaction,
            middleReaction: middleReaction,
            rightReaction: rightReaction
        };
    }
    getDeflectionEquation(beam, load) {
        var state = this.getState(beam, load);

        return function (x) {
            if (x < 0 || x > state.totalSpan) {
                return {
                    x: x,
                    y: null
                };
            }

            if (x <= state.primarySpan) {
                return {
                    x: x,
                    y: (
                        x * (
                            4 * state.leftReaction * Math.pow(x, 2) -
                            state.load * Math.pow(x, 3) +
                            state.load * Math.pow(state.primarySpan, 3) -
                            4 * state.leftReaction * Math.pow(state.primarySpan, 2)
                        ) / (24 * state.EI)
                    ) * 1000 * state.j2
                };
            }

            return {
                x: x,
                y: (
                    (
                        (state.leftReaction * x / 6) * (
                            Math.pow(x, 2) - Math.pow(state.primarySpan, 2)
                        ) +
                        (state.middleReaction * x / 6) * (
                            Math.pow(x, 2) -
                            3 * state.primarySpan * x +
                            3 * Math.pow(state.primarySpan, 2)
                        ) -
                        (state.middleReaction * Math.pow(state.primarySpan, 3) / 6) -
                        (state.load * x / 24) * (
                            Math.pow(x, 3) - Math.pow(state.primarySpan, 3)
                        )
                    ) / state.EI
                ) * 1000 * state.j2
            };
        };
    }
    getBendingMomentEquation(beam, load) {
        var state = this.getState(beam, load);

        return function (x) {
            if (x < 0 || x > state.totalSpan) {
                return {
                    x: x,
                    y: null
                };
            }

            return {
                x: x,
                y: x <= state.primarySpan
                    ? state.leftReaction * x - (state.load * Math.pow(x, 2)) / 2
                    : (
                        state.leftReaction * x +
                        state.middleReaction * (x - state.primarySpan) -
                        (state.load * Math.pow(x, 2)) / 2
                    )
            };
        };
    }
    getShearForceEquation(beam, load) {
        var state = this.getState(beam, load);

        return function (x, side) {
            var leftValue;

            if (x < 0 || x > state.totalSpan) {
                return {
                    x: x,
                    y: null
                };
            }

            if (BeamAnalysisHelper.isAt(x, state.primarySpan)) {
                leftValue = state.leftReaction - (state.load * state.primarySpan);

                return {
                    x: x,
                    y: side === 'right'
                        ? leftValue + state.middleReaction
                        : leftValue
                };
            }

            return {
                x: x,
                y: x < state.primarySpan
                    ? state.leftReaction - (state.load * x)
                    : state.leftReaction + state.middleReaction - (state.load * x)
            };
        };
    }
};
