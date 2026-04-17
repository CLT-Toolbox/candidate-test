(function (global) {
    'use strict';

    // Helper hitungan rumus struktur
    const StructuralPhysics = {

        // Simply Supported
        calculateSSMoment: (x, L, w) => -1 * (w * x / 2) * (L - x),
        calculateSSShear: (x, L, w) => w * ((L / 2) - x),
        calculateSSDeflection: (x, L, w, EI) => {
            const y = (w * x / (24 * EI)) * (Math.pow(L, 3) - 2 * L * Math.pow(x, 2) + Math.pow(x, 3));
            return -y;
        },

        // Two Span Unequal
        getTwoSpanContext: (w, L1, L2, EI) => {
            const MB = (L1 + L2 === 0) ? 0 : -w * (Math.pow(L1, 3) + Math.pow(L2, 3)) / (8 * (L1 + L2));
            const R1 = (w * L1 / 2) + (MB / L1);
            const R2 = (w * L1 / 2) - (MB / L1) + (w * L2 / 2) - (MB / L2);
            const R3 = (w * L2 / 2) + (MB / L2);
            return { w, L1, L2, EI, MB, R1, R2, R3 };
        },

        calculateTwoSpanMoment: (x, ctx) => {
            const { w, L1, L2, MB, R1 } = ctx;
            let M;
            if (x <= L1) {
                M = R1 * x - (w * Math.pow(x, 2) / 2);
            } else {
                const z = (L1 + L2) - x;
                const RC = (w * L2 / 2) + (MB / L2);
                M = RC * z - (w * Math.pow(z, 2) / 2);
            }
            return -1 * M;
        },

        calculateTwoSpanShear: (x, ctx, side = 'auto') => {
            const { w, L1, R1, R2 } = ctx;
            if (side === 'left' || (side === 'auto' && x < L1)) {
                return R1 - w * x;
            } else {
                return R1 + R2 - w * x;
            }
        },

        calculateTwoSpanDeflection: (x, ctx) => {
            const { w, L1, EI, R1, R2 } = ctx;
            try {
                if (x <= L1) {
                    const bracket = (4 * R1 * Math.pow(x, 2)) - (w * Math.pow(x, 3)) + (w * Math.pow(L1, 3)) - (4 * R1 * Math.pow(L1, 2));
                    return (x / (24 * EI)) * bracket;
                } else {
                    const termA = (R1 * x / 6) * (Math.pow(x, 2) - Math.pow(L1, 2));
                    const termB = (R2 * x / 6) * (Math.pow(x, 2) - (3 * L1 * x) + (3 * Math.pow(L1, 2)));
                    const termC = (R2 * Math.pow(L1, 3)) / 6;
                    const termD = (w * x / 24) * (Math.pow(x, 3) - Math.pow(L1, 3));
                    return (termA + termB - termC - termD) * (1 / EI);
                }
            } catch (e) {
                return 0;
            }
        }
    };

    global.StructuralPhysics = StructuralPhysics;

})(typeof window !== 'undefined' ? window : global);
