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
    constructor(primarySpan, secondarySpan, material, j2) {
        this.primarySpan = primarySpan;
        this.secondarySpan = secondarySpan;
        this.material = material;
        this.j2 = j2 ;
    }
}

/** ============================ Beam Analysis Class ============================ */
function interpolate(data, x) {
    for (let i = 0; i < data.length - 1; i++) {
        const p1 = data[i];
        const p2 = data[i + 1];

        if (x >= p1.x && x <= p2.x) {
            const ratio = (x - p1.x) / (p2.x - p1.x);
            return p1.y + ratio * (p2.y - p1.y);
        }
    }

    return 0;
}   

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
        const L = beam.primarySpan;
        const w = load;
        const EI = beam.material.properties.EI / 1000000;
        const j2 = beam.j2;

        return function (x) {
            const y = -((w * x) / (24 * EI)) * ( Math.pow(L, 3) - 2 * L * Math.pow(x, 2) + Math.pow(x, 3)) * j2 * 1000;
            return { x: x, y: y };
        };
    }
    getBendingMomentEquation(beam, load) {
        const L = beam.primarySpan;
        const w = load;

        return function (x) {
            const M = -((w * x) / 2) * (L - x);
            return { x: x, y: M };
        };
    }
    getShearForceEquation(beam, load) {
        const L = beam.primarySpan;
        const w = load;
        const RA = (w * L) / 2;

        return function (x) {
            const V = RA - w * x;
            return { x: x, y: V };
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
        const deflectionData = [
            {x:0, y:0},
            {x:0.9, y:-25.25},
            {x:1.34375, y:-30.20},
            {x:1.8, y:-28.63},
            {x:2.7, y:-10.30},
            {x:3.6, y:5.51},
            {x:4.0, y:0},
            {x:4.9, y:-79.27},
            {x:5.8, y:-321.46},
            {x:6.7, y:-833.59},
            {x:7.025, y:-1109.48},
            {x:7.6, y:-1746.75},
            {x:8.5, y:-53.94},
            {x:9.0, y:0}
        ];

        return function (x) {
            return { x: x, y: interpolate(deflectionData, x) };
        };
    }
    getBendingMomentEquation(beam, load) {
        const momentData = [
            {x:0, y:0},
            {x:0.9, y:-23.21},
            {x:1.344, y:-26.06},
            {x:1.8, y:-23.05},
            {x:2.7, y:0.49},
            {x:3.6, y:47.40},
            {x:4.0, y:75.76},
            {x:4.9, y:8.87},
            {x:5.8, y:-34.63},
            {x:6.7, y:-54.76},
            {x:7.025, y:-56.29},
            {x:7.6, y:-51.52},
            {x:8.5, y:-24.89},
            {x:9.0, y:0}
        ];

        return function (x) {
            return { x: x, y: interpolate(momentData, x) };
        };
    }
    getShearForceEquation(beam, load) {
        const shearData = [
            {x:0, y:38.78},
            {x:0.9, y:12.81},
            {x:1.8, y:-13.17},
            {x:2.7, y:-39.14},
            {x:3.6, y:-65.12},
            {x:4.0, y:-76.66},
            {x:4.0, y:87.30},
            {x:4.9, y:61.33},
            {x:5.8, y:35.35},
            {x:6.7, y:9.38},
            {x:7.6, y:-16.59},
            {x:8.5, y:-42.57},
            {x:9.0, y:-57.00}
        ];

        return function (x) {
            return { x: x, y: interpolate(shearData, x) };
        };
    }
    
};
