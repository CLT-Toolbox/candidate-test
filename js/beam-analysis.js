class BeamAnalysis {
    static analyzer = class {
        constructor(config) {
            this.type = config.type;
            this.w = config.w;
            this.EI_Nmm2 = config.EI;
            this.EI = config.EI / 1000000000;
            
            if (this.type === 'simply-supported') {
                this.L = config.L;
                this.j2 = config.j2 || 1;
            } 
            else if (this.type === 'two-span') {
                this.L1 = config.L1;
                this.L2 = config.L2;
                this.L = this.L1 + this.L2;
            }
        }

        calculate() {
            if (this.type === 'simply-supported') {
                return this.calculateSimplySupported();
            } 
            else if (this.type === 'two-span') {
                return this.calculateTwoSpan();
            }
        }

        calculateSimplySupported() {
            const { w, L, EI, j2 } = this;
            const Ra = (w * L) / 2;
            const Rb = Ra;
            const data = [];
            const step = 0.1;
            
            for (let x = 0; x <= L + 0.001; x += step) {
                const xFixed = parseFloat(x.toFixed(2));
                const shear = w * ((L / 2) - xFixed);
                const moment = ((w * xFixed / 2) * (L - xFixed)) * -1;
                const L3 = Math.pow(L, 3);
                const x2 = Math.pow(xFixed, 2);
                const x3 = Math.pow(xFixed, 3);
                const deflection = -((w * xFixed) / (24 * EI)) * 
                                   (L3 - 2 * L * x2 + x3) * j2 * 1000;
                
                data.push({
                    x: xFixed,
                    shear: parseFloat(shear.toFixed(4)),
                    moment: parseFloat(moment.toFixed(4)),
                    deflection: parseFloat(deflection.toFixed(4))
                });
            }

            return {
                type: 'simply-supported',
                reactions: { Ra, Rb },
                L, w, EI, j2,
                data,
                maxMoment: Math.max(...data.map(d => Math.abs(d.moment))),
                maxShear: Math.max(...data.map(d => Math.abs(d.shear))),
                maxDeflection: Math.max(...data.map(d => Math.abs(d.deflection)))
            };
        }

        calculateTwoSpan() {
            const { w, L1, L2, EI } = this;
            const L = L1 + L2;
            const M1 = -(w * Math.pow(L2, 3) + w * Math.pow(L1, 3)) / (8 * (L1 + L2));
            const R1 = -M1 / L1 + (w * L1) / 2;
            const R3 = -M1 / L2 + (w * L2) / 2;
            const R2 = w * L1 + w * L2 - R1 - R3;

            const data = [];
            const step = 0.1;
            
            for (let x = 0; x <= L + 0.001; x += step) {
                const xFixed = parseFloat(x.toFixed(2));
                let shear, moment;
                
                if (xFixed === 0) {
                    shear = R1;
                } 
                else if (xFixed > 0 && xFixed < L1) {
                    shear = R1 - (w * xFixed);
                } 
                else if (Math.abs(xFixed - L1) < 0.001) {
                    shear = R1 - (w * L1);
                } 
                else if (xFixed > L1 && xFixed <= L) {
                    shear = R1 + R2 - (w * xFixed);
                }

                if (xFixed <= L1) {
                    moment = R1 * xFixed - (w * Math.pow(xFixed, 2)) / 2;
                } else {
                    moment = R1 * xFixed + R2 * (xFixed - L1) - (w * Math.pow(xFixed, 2)) / 2;
                }

                data.push({
                    x: xFixed,
                    shear: parseFloat(shear.toFixed(4)),
                    moment: parseFloat(moment.toFixed(4)),
                    deflection: 0
                });
            }

            return {
                type: 'two-span',
                reactions: { R1, R2, R3, M1 },
                L1, L2, L, w, EI,
                data,
                maxMoment: Math.max(...data.map(d => Math.abs(d.moment))),
                maxShear: Math.max(...data.map(d => Math.abs(d.shear)))
            };
        }
    };
}