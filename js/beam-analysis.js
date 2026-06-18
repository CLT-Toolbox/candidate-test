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
            'two-span-equal': new BeamAnalysis.analyzer.twoSpanEqual(),
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
        const L = beam.primarySpan * 1000;  // Convert to mm
        const EI = beam.material.properties.EI;
        const w = load;  // kN/m = N/mm

        return function (x) {
            const xMm = x * 1000;  // Convert to mm
            const deflection = (-w * xMm / (24 * EI)) * (Math.pow(L, 3) - 2 * L * Math.pow(xMm, 2) + Math.pow(xMm, 3));
            return {
                x: x,
                y: deflection  // in mm
            };
        };
    }


    getBendingMomentEquation(beam, load) {
        const L = beam.primarySpan;
        const w = load;

        return function (x) {
            const moment = w * x / 2 * (L - x);
            return {
                x: x,
                y: moment  // in kNm
            };
        };
    }


    getShearForceEquation(beam, load) {
        const L = beam.primarySpan;
        const w = load;

        return function (x) {
            const shearForce = w * (L / 2 - x);
            return {
                x: x,
                y: shearForce  // in kN
            };
        };
    }
};


/**
 * Calculate deflection, bending stress and shear stress for a beam with two equal spans
 *
 * @param {Beam}   beam   The beam object
 * @param {Number}  load    The applied load
 */
BeamAnalysis.analyzer.twoSpanEqual = class {
    constructor(beam, load) {
        this.beam = beam;
        this.load = load;
    }

    calculateReactions(beam, load) {
        const L = beam.primarySpan;
        const w = load;


        const R1 = w * L / 2;
        const R3 = w * L / 2;
        const R2 = 2 * w * L - R1 - R3;
        const M0 = R1 * L - w * L * L / 2;

        return { R1, R2, R3, M0 };
    }


    getDeflectionEquation(beam, load) {
        const L = beam.primarySpan; 
        const w = load;
        const EI = beam.material.properties.EI;
        
        const L_mm = L * 1000;
        const w_N_per_mm = w / 1000;
        
        const reactions = this.calculateReactions(beam, load);
        const R1 = reactions.R1 * 1000;
        const R3 = reactions.R3 * 1000;
        const M0 = reactions.M0 * 1e6;

        return (x) => {
            let deflection;
            const x_mm = x * 1000;

            if (x <= L) {
                const term1 = (R1 * Math.pow(x_mm, 3)) / (6 * EI);
                const term2 = (M0 * Math.pow(x_mm, 2)) / (2 * EI);
                const term3 = (w_N_per_mm * Math.pow(x_mm, 4)) / (24 * EI);
                const term4 = (w_N_per_mm * L_mm * Math.pow(x_mm, 2)) / (12 * EI);
                deflection = term1 + term2 - term3 - term4;
            } else {
                const x2 = x - L;
                const x2_mm = x2 * 1000;
                
                const delta_L = (R1 * Math.pow(L_mm, 3)) / (6 * EI) + 
                               (M0 * Math.pow(L_mm, 2)) / (2 * EI) - 
                               (w_N_per_mm * Math.pow(L_mm, 4)) / (24 * EI) - 
                               (w_N_per_mm * L_mm * Math.pow(L_mm, 2)) / (12 * EI);

                const term1 = (R3 * Math.pow(x2_mm, 3)) / (6 * EI);
                const term2 = (M0 * Math.pow(x2_mm, 2)) / (2 * EI);
                const term3 = (w_N_per_mm * Math.pow(x2_mm, 4)) / (24 * EI);
                const term4 = (w_N_per_mm * L_mm * Math.pow(x2_mm, 2)) / (12 * EI);

                deflection = delta_L + term1 + term2 - term3 - term4;
            }

            return { x: x, y: deflection };
        };
    }

    getBendingMomentEquation(beam, load) {
        const L = beam.primarySpan;
        const w = load;

        const reactions = this.calculateReactions(beam, load);
        const R1 = reactions.R1;
        const R3 = reactions.R3;

        return (x) => {
            let moment;

            if (x <= L) {
                moment = R1 * x - w * x * x / 2;
            } else {
                const dist = 2 * L - x;
                moment = R3 * dist - w * dist * dist / 2;
            }

            return { x: x, y: moment };
        };
    }

    getShearForceEquation(beam, load) {
        const L = beam.primarySpan;
        const w = load;

        const reactions = this.calculateReactions(beam, load);
        const R1 = reactions.R1;
        const R3 = reactions.R3;

        return (x) => {
            let shearForce;

            if (x < L) {
                shearForce = R1 - w * x;
            } else {
                shearForce = R3 - w * (2 * L - x);
            }

            return { x: x, y: shearForce };
        };
    }
};


/**
 * Calculate deflection, bending stress and shear stress for a beam with two spans of unequal condition
 *
 * @param {Beam}   beam   The beam object
 * @param {Number}  load    The applied load
 */
BeamAnalysis.analyzer.twoSpanUnequal = class {
    constructor(beam, load) {
        this.beam = beam;
        this.load = load;
    }

    calculateReactions(beam, load) {
        const L1 = beam.primarySpan;
        const L2 = beam.secondarySpan;
        const w = load;
        const M0 = -w * (Math.pow(L1, 3) + Math.pow(L2, 3)) / (8 * (L1 + L2));
        const R1 = M0 / L1 + w * L1 / 2;
        const R3 = -M0 / L2 + w * L2 / 2;
        const R2 = w * L1 + w * L2 - R1 - R3;

        return { R1, R2, R3, M0 };
    }

    getDeflectionEquation(beam, load) {
        const L1 = beam.primarySpan; 
        const L2 = beam.secondarySpan;  
        const w = load;  
        const EI = beam.material.properties.EI; 

        const L1_mm = L1 * 1000;
        const L2_mm = L2 * 1000;
        const w_N_per_mm = w / 1000;  // N/mm
        
        const reactions = this.calculateReactions(beam, load);
        const R1 = reactions.R1 * 1000;
        const R3 = reactions.R3 * 1000; 
        const M0 = reactions.M0 * 1e6;  

        return (x) => {
            let deflection;
            const x_mm = x * 1000;

            if (x <= L1) {
                const term1 = (R1 * Math.pow(x_mm, 3)) / (6 * EI);
                const term2 = (M0 * Math.pow(x_mm, 2)) / (2 * EI);
                const term3 = (w_N_per_mm * Math.pow(x_mm, 4)) / (24 * EI);
                const term4 = (w_N_per_mm * L1_mm * Math.pow(x_mm, 2)) / (12 * EI);

                deflection = term1 + term2 - term3 - term4;
            } else {
                const x2 = x - L1;  
                const x2_mm = x2 * 1000;
                const delta_L1 = (R1 * Math.pow(L1_mm, 3)) / (6 * EI) + 
                                (M0 * Math.pow(L1_mm, 2)) / (2 * EI) - 
                                (w_N_per_mm * Math.pow(L1_mm, 4)) / (24 * EI) - 
                                (w_N_per_mm * L1_mm * Math.pow(L1_mm, 2)) / (12 * EI);
                const term1 = (R3 * Math.pow(x2_mm, 3)) / (6 * EI);
                const term2 = (M0 * Math.pow(x2_mm, 2)) / (2 * EI);
                const term3 = (w_N_per_mm * Math.pow(x2_mm, 4)) / (24 * EI);
                const term4 = (w_N_per_mm * L2_mm * Math.pow(x2_mm, 2)) / (12 * EI);

                deflection = delta_L1 + term1 + term2 - term3 - term4;
            }

            return {
                x: x,
                y: deflection 
            };
        };
    }
    getBendingMomentEquation(beam, load) {
        const L1 = beam.primarySpan;
        const L2 = beam.secondarySpan;
        const w = load;

        const reactions = this.calculateReactions(beam, load);
        const R1 = reactions.R1;
        const M0 = reactions.M0;

        return (x) => {
            let moment;

            if (x <= L1) {
                moment = R1 * x - w * x * x / 2;
            } else {
                const x2 = x - L1;
                const R3 = -M0 / L2 + w * L2 / 2;
                moment = -R3 * x2 + w * x2 * x2 / 2 + M0;
            }

            return {
                x: x,
                y: moment  // in kNm
            };
        };
    }

    getShearForceEquation(beam, load) {
        const L1 = beam.primarySpan;
        const L2 = beam.secondarySpan;
        const w = load;

        const reactions = this.calculateReactions(beam, load);
        const R1 = reactions.R1;
        const R3 = reactions.R3;

        return (x) => {
            let shearForce;

            if (x < L1) {
                shearForce = R1 - w * x;
            } else {
                shearForce = -(R3 + w * (x - L1));
            }

            return {
                x: x,
                y: shearForce  // in kN
            };
        };
    }
};
