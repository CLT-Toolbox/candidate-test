'use strict';

class Material {
    constructor(name, properties) {
        this.name = name;
        this.properties = properties;
    }
}

class Beam {
    constructor(primarySpan, secondarySpan, material) {
        this.primarySpan = primarySpan;
        this.secondarySpan = secondarySpan;
        this.material = material;
    }
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

BeamAnalysis.analyzer = {};

BeamAnalysis.analyzer.simplySupported = class {
    constructor(beam, load) {
        this.beam = beam;
        this.load = load;
    }

    getShearForceEquation(beam, load) {
        var L = beam.primarySpan;
        var w = load;
        var Ra = w * L / 2;
        return function(x) {
            if (x < 0 || x > L) {
                return { x: x, y: 0 };
            }
            return { x: x, y: Ra - w * x };
        };
    }

    getBendingMomentEquation(beam, load) {
        var L = beam.primarySpan;
        var w = load;
        var Ra = w * L / 2;
        return function(x) {
            if (x < 0 || x > L) {
                return { x: x, y: 0 };
            }
            return { x: x, y: Ra * x - (w * x * x) / 2 };
        };
    }

    getDeflectionEquation(beam, load) {
        var L = beam.primarySpan;
        var w = load;
        var j2 = beam.material.properties.j2 || 1;
        var EI = beam.material.properties.EI / 1e9;
        return function(x) {
            if (x < 0 || x > L) {
                return { x: x, y: 0 };
            }
            var y = (w * x / (24 * EI)) * (L * L * L - 2 * L * x * x + x * x * x);
            return { x: x, y: y * j2 * 1000 };
        };
    }
};

BeamAnalysis.analyzer.twoSpanUnequal = class {
    constructor(beam, load) {
        this.beam = beam;
        this.load = load;
    }

    _getReactions(L1, L2, w) {
        var MB = -(w / 8) * (L1 * L1 * L1 + L2 * L2 * L2) / (L1 + L2);
        var Ra = (w * L1 / 2) + (MB / L1);
        var Rc = (w * L2 / 2) + (MB / L2);
        var Rb = w * (L1 + L2) - Ra - Rc;
        return { Ra: Ra, Rb: Rb, Rc: Rc, MB: MB };
    }

    getShearForceEquation(beam, load) {
        var L1 = beam.primarySpan;
        var L2 = (!beam.secondarySpan || isNaN(beam.secondarySpan) || beam.secondarySpan <= 0) ? L1 : beam.secondarySpan;
        var w = load;
        var r = this._getReactions(L1, L2, w);
        return function(x) {
            if (x < 0 || x > L1 + L2) {
                return { x: x, y: 0 };
            }
            if (x < L1) {
                return { x: x, y: r.Ra - w * x };
            }
            return { x: x, y: r.Ra + r.Rb - w * x };
        };
    }

    getBendingMomentEquation(beam, load) {
        var L1 = beam.primarySpan;
        var L2 = (!beam.secondarySpan || isNaN(beam.secondarySpan) || beam.secondarySpan <= 0) ? L1 : beam.secondarySpan;
        var w = load;
        var r = this._getReactions(L1, L2, w);
        return function(x) {
            if (x < 0 || x > L1 + L2) {
                return { x: x, y: 0 };
            }
            if (x <= L1) {
                return { x: x, y: -(r.Ra * x - (w * x * x) / 2) };
            }
            var x2 = x - L1;
            return { x: x, y: -(r.Rc * (L2 - x2) - (w * (L2 - x2) * (L2 - x2)) / 2) };
        };
    }

    getDeflectionEquation(beam, load) {
        var L1 = beam.primarySpan;
        var L2 = (!beam.secondarySpan || isNaN(beam.secondarySpan) || beam.secondarySpan <= 0) ? L1 : beam.secondarySpan;
        var w = load;
        var j2 = beam.material.properties.j2 || 1;
        var EI = beam.material.properties.EI / 1e9;
        var r = this._getReactions(L1, L2, w);
        var Ra = r.Ra;
        var Rb = r.Rb;
        var C1 = -(Ra * L1 * L1 / 6 - w * L1 * L1 * L1 / 24);
        return function(x) {
            if (x < 0 || x > L1 + L2) {
                return { x: x, y: 0 };
            }
            var bracket = x > L1 ? (x - L1) : 0;
            var y = (Ra * x * x * x / 6 - w * x * x * x * x / 24 + Rb * bracket * bracket * bracket / 6 + C1 * x) / EI;
            return { x: x, y: y * j2 * 1000 };
        };
    }
};