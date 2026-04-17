(function (global) {
    'use strict';

    // Model data Balok & Material
    class Beam {
        constructor() {
            this.primarySpan = 0;
            this.secondarySpan = 0;
            this.material = null;
        }
    }

    class Material {
        constructor(name, properties) {
            this.name = name;
            this.properties = properties;
        }
    }

    // Engine Utama Analisis
    class BeamAnalysis {
        constructor() {
            this.analyzers = {
                'simply-supported': new SimplySupportedAnalyzer(),
                'two-span-unequal': new TwoSpanUnequalAnalyzer()
            };
        }

        getAnalyzer(condition) {
            return this.analyzers[condition];
        }

        getDeflection(beam, load, condition) {
            const analyzer = this.getAnalyzer(condition);
            return {
                beam: beam,
                load: load,
                ...analyzer.getDeflectionEquation(beam, load)
            };
        }

        getBendingMoment(beam, load, condition) {
            const analyzer = this.getAnalyzer(condition);
            return {
                beam: beam,
                load: load,
                ...analyzer.getBendingMomentEquation(beam, load)
            };
        }

        getShearForce(beam, load, condition) {
            const analyzer = this.getAnalyzer(condition);
            return {
                beam: beam,
                load: load,
                ...analyzer.getShearForceEquation(beam, load)
            };
        }
    }

    class BaseAnalyzer {
        getParams(beam, load) {
            const w = (parseFloat(load) || 0);
            const L1 = Math.max(0.001, parseFloat(beam.primarySpan) || 0);
            const L2 = Math.max(0, parseFloat(beam.secondarySpan) || 0);
            const EI = (beam.material && beam.material.properties && beam.material.properties.EI)
                ? beam.material.properties.EI / 1e9
                : 1;
            return { w, L1, L2, EI };
        }
    }

    class SimplySupportedAnalyzer extends BaseAnalyzer {
        getDeflectionEquation(beam, load) {
            const { w, L1, EI } = this.getParams(beam, load);
            return {
                equation: (x) => ({ x: x, y: StructuralPhysics.calculateSSDeflection(x, L1, w, EI) * 1000 })
            };
        }

        getBendingMomentEquation(beam, load) {
            const { w, L1 } = this.getParams(beam, load);
            return {
                equation: (x) => ({ x: x, y: StructuralPhysics.calculateSSMoment(x, L1, w) })
            };
        }

        getShearForceEquation(beam, load) {
            const { w, L1 } = this.getParams(beam, load);
            return {
                equation: (x) => ({ x: x, y: StructuralPhysics.calculateSSShear(x, L1, w) })
            };
        }
    }

    class TwoSpanUnequalAnalyzer extends BaseAnalyzer {
        getDeflectionEquation(beam, load) {
            const { w, L1, L2, EI } = this.getParams(beam, load);
            const ctx = StructuralPhysics.getTwoSpanContext(w, L1, L2, EI);
            return {
                discontinuities: [L1],
                equation: (x) => ({ x: x, y: StructuralPhysics.calculateTwoSpanDeflection(x, ctx) * 1000 })
            };
        }

        getBendingMomentEquation(beam, load) {
            const { w, L1, L2, EI } = this.getParams(beam, load);
            const ctx = StructuralPhysics.getTwoSpanContext(w, L1, L2, EI);
            return {
                discontinuities: [L1],
                equation: (x) => ({ x: x, y: StructuralPhysics.calculateTwoSpanMoment(x, ctx) })
            };
        }

        getShearForceEquation(beam, load) {
            const { w, L1, L2, EI } = this.getParams(beam, load);
            const ctx = StructuralPhysics.getTwoSpanContext(w, L1, L2, EI);
            return {
                discontinuities: [L1],
                equation: (x, side = 'auto') => ({
                    x: x,
                    y: StructuralPhysics.calculateTwoSpanShear(x, ctx, side)
                })
            };
        }
    }

    global.Beam = Beam;
    global.Material = Material;
    global.BeamAnalysis = BeamAnalysis;

})(typeof window !== 'undefined' ? window : global);
