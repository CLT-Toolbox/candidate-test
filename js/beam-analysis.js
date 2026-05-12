'use strict';

/** =========================================================
 * BEAM ANALYSIS
 * MATCH EXCEL + GRAPH
 * ========================================================= */

class Material {

    constructor(name, properties = {}) {

        this.name = name;

        this.properties = {

            EI:
                Number(properties.EI || 1),

            GA:
                Number(properties.GA || 0),

            j2:
                Number(properties.j2 || 1)
        };
    }
}

class Beam {

    constructor(
        primarySpan,
        secondarySpan,
        material
    ) {

        this.primarySpan =
            Number(primarySpan || 0);

        this.secondarySpan =
            Number(secondarySpan || 0);

        this.material =
            material;
    }

    getTotalLength() {

        return (

            this.primarySpan +

            this.secondarySpan
        );
    }
}

class BeamAnalysis {

    constructor() {

        this.analyzers = {

            'simply-supported':

                new BeamAnalysis
                    .analyzer
                    .simplySupported(),

            'two-span-unequal':

                new BeamAnalysis
                    .analyzer
                    .twoSpanUnequal()
        };
    }

    getAnalyzer(condition) {

        const analyzer =
            this.analyzers[condition];

        if (!analyzer) {

            throw new Error(
                'Invalid condition'
            );
        }

        return analyzer;
    }

    getDeflection(
        beam,
        load,
        condition
    ) {

        return {

            beam,
            load,

            equation:

                this
                    .getAnalyzer(condition)
                    .getDeflectionEquation(
                        beam,
                        load
                    )
        };
    }

    getBendingMoment(
        beam,
        load,
        condition
    ) {

        return {

            beam,
            load,

            equation:

                this
                    .getAnalyzer(condition)
                    .getBendingMomentEquation(
                        beam,
                        load
                    )
        };
    }

    getShearForce(
        beam,
        load,
        condition
    ) {

        return {

            beam,
            load,

            equation:

                this
                    .getAnalyzer(condition)
                    .getShearForceEquation(
                        beam,
                        load
                    )
        };
    }
}

BeamAnalysis.analyzer = {};

/** =========================================================
 * SIMPLY SUPPORTED
 * ========================================================= */

BeamAnalysis.analyzer.simplySupported = class {

    constructor() {

        this.EPSILON = 1e-9;
    }

    isEqual(a, b) {

        return (
            Math.abs(a - b) <
            this.EPSILON
        );
    }

    /** =====================================================
     * REACTION
     * ===================================================== */

    getReaction(
        beam,
        load
    ) {

        const L =
            beam.primarySpan;

        const w =
            Number(load);

        const R =

            (
                w * L
            ) / 2;

        return {

            R1:
                Number(
                    R.toFixed(6)
                ),

            R2:
                Number(
                    R.toFixed(6)
                )
        };
    }

    /** =====================================================
     * SHEAR
     * ===================================================== */

    getShearForceEquation(
        beam,
        load
    ) {

        const L =
            beam.primarySpan;

        const w =
            Number(load);

        const reaction =
            this.getReaction(
                beam,
                load
            );

        return (x) => {

            x = Number(x);

            let V =

                reaction.R1 -

                (
                    w * x
                );

            /**
             * exact support
             */

            if (
                this.isEqual(x, L)
            ) {

                V = -reaction.R2;
            }

            return {

                x,

                y:
                    Number(
                        V.toFixed(6)
                    )
            };
        };
    }

    /** =====================================================
     * BENDING
     * ===================================================== */

    getBendingMomentEquation(
        beam,
        load
    ) {

        const L =
            beam.primarySpan;

        const w =
            Number(load);

        const reaction =
            this.getReaction(
                beam,
                load
            );

        return (x) => {

            x = Number(x);

            let M =

                (
                    reaction.R1 * x
                ) -

                (
                    w *
                    Math.pow(x, 2) / 2
                );

            /**
             * Excel convention
             * sagging negative
             */

            M = -M;

            /**
             * supports = 0
             */

            if (
                this.isEqual(x, 0) ||
                this.isEqual(x, L)
            ) {

                M = 0;
            }

            return {

                x,

                y:
                    Number(
                        M.toFixed(6)
                    )
            };
        };
    }

    /** =====================================================
     * DEFLECTION
     * ===================================================== */

    getDeflectionEquation(
        beam,
        load
    ) {

        const L =
            beam.primarySpan;

        const EI =
            beam
                .material
                .properties
                .EI;

        const j2 =
            beam
                .material
                .properties
                .j2 || 1;

        const w =
            Number(load);

        const EI_CONVERTED =
            EI / Math.pow(1000, 3);

        return (x) => {

            x = Number(x);

            let y =

                -(
                    (
                        w * x
                    ) /

                    (
                        24 *
                        EI_CONVERTED
                    )
                ) *

                (
                    Math.pow(L, 3) -

                    (
                        2 *
                        L *
                        Math.pow(x, 2)
                    ) +

                    Math.pow(x, 3)
                );

            /**
             * scale
             */

            y =
                y *
                1000 *
                j2;

            /**
             * supports = 0
             */

            if (
                this.isEqual(x, 0) ||
                this.isEqual(x, L)
            ) {

                y = 0;
            }

            return {

                x,

                y:
                    Number(
                        y.toFixed(6)
                    )
            };
        };
    }

    /** =====================================================
     * GRAPH POINTS
     * ===================================================== */

    getCriticalPoints(
        beam
    ) {

        return [

            0,

            beam.primarySpan / 2,

            beam.primarySpan
        ];
    }
};

/** =========================================================
 * TWO SPAN UNEQUAL
 * ========================================================= */

BeamAnalysis.analyzer.twoSpanUnequal = class {

    constructor() {

        this.EPSILON = 1e-9;
    }

    isEqual(a, b) {

        return (
            Math.abs(a - b) <
            this.EPSILON
        );
    }

    /** =====================================================
     * REACTION
     * EXACT EXCEL
     * ===================================================== */

    getReaction(
        beam,
        load
    ) {

        const L1 =
            beam.primarySpan;

        const L2 =
            beam.secondarySpan;

        const w =
            Number(load);

        const total =
            L1 + L2;

        /**
         * exact excel
         */

        const M1 =

            -(
                (
                    w *
                    Math.pow(L1, 3)
                ) +

                (
                    w *
                    Math.pow(L2, 3)
                )
            ) /

            (
                8 * total
            );

        const R1 =

            (
                w * L1 / 2
            ) +

            (
                M1 / L1
            );

        const R3 =

            (
                w * L2 / 2
            ) +

            (
                M1 / L2
            );

        const R2 =

            (
                w * total
            ) -

            R1 -

            R3;

        return {

            M1:
                Number(
                    M1.toFixed(6)
                ),

            R1:
                Number(
                    R1.toFixed(6)
                ),

            R2:
                Number(
                    R2.toFixed(6)
                ),

            R3:
                Number(
                    R3.toFixed(6)
                )
        };
    }

    /** =====================================================
     * SHEAR
     * ===================================================== */

    getShearForceEquation(
        beam,
        load
    ) {

        const L1 =
            beam.primarySpan;

        const total =
            beam.getTotalLength();

        const w =
            Number(load);

        const reaction =
            this.getReaction(
                beam,
                load
            );

        return (x) => {

            x = Number(x);

            let V = 0;

            /**
             * LEFT SPAN
             */

            if (x < L1) {

                V =

                    reaction.R1 -

                    (
                        w * x
                    );
            }

            /**
             * MID SUPPORT
             */

            else if (
                this.isEqual(x, L1)
            ) {

                /**
                 * IMPORTANT:
                 * graph vertical jump
                 */

                V =

                    reaction.R1 -

                    (
                        w * x
                    ) +

                    reaction.R2;
            }

            /**
             * RIGHT SPAN
             */

            else {

                V =

                    (
                        reaction.R1 +
                        reaction.R2
                    ) -

                    (
                        w * x
                    );
            }

            /**
             * right support
             */

            if (
                this.isEqual(x, total)
            ) {

                V = -reaction.R3;
            }

            return {

                x,

                y:
                    Number(
                        V.toFixed(6)
                    )
            };
        };
    }

    /** =====================================================
     * BENDING
     * ===================================================== */

    getBendingMomentEquation(
        beam,
        load
    ) {

        const L1 =
            beam.primarySpan;

        const total =
            beam.getTotalLength();

        const w =
            Number(load);

        const reaction =
            this.getReaction(
                beam,
                load
            );

        return (x) => {

            x = Number(x);

            let M = 0;

            /**
             * left span
             */

            if (x <= L1) {

                M =

                    (
                        reaction.R1 * x
                    ) -

                    (
                        w *
                        Math.pow(x, 2) / 2
                    );
            }

            /**
             * right span
             */

            else {

                M =

                    (
                        reaction.R1 * x
                    ) +

                    (
                        reaction.R2 *
                        (
                            x - L1
                        )
                    ) -

                    (
                        w *
                        Math.pow(x, 2) / 2
                    );
            }

            /**
             * Excel convention
             */

            M = -M;

            /**
             * exact support moment
             */

            if (
                this.isEqual(x, L1)
            ) {

                M = reaction.M1;
            }

            /**
             * supports zero
             */

            if (
                this.isEqual(x, 0) ||
                this.isEqual(x, total)
            ) {

                M = 0;
            }

            return {

                x,

                y:
                    Number(
                        M.toFixed(6)
                    )
            };
        };
    }

    /** =====================================================
     * DEFLECTION
     * ===================================================== */

    getDeflectionEquation(
        beam,
        load
    ) {

        const L1 =
            beam.primarySpan;

        const total =
            beam.getTotalLength();

        const EI =
            beam
                .material
                .properties
                .EI;

        const j2 =
            beam
                .material
                .properties
                .j2 || 1;

        const w =
            Number(load);

        const reaction =
            this.getReaction(
                beam,
                load
            );

        const EI_CONVERTED =
            EI / Math.pow(1000, 3);

        return (x) => {

            x = Number(x);

            let y = 0;

            /**
             * LEFT SPAN
             */

            if (x <= L1) {

                y =

                    (
                        x /

                        (
                            24 *
                            EI_CONVERTED
                        )
                    ) *

                    (
                        (
                            4 *
                            reaction.R1 *
                            Math.pow(x, 2)
                        ) -

                        (
                            w *
                            Math.pow(x, 3)
                        ) +

                        (
                            w *
                            Math.pow(L1, 3)
                        ) -

                        (
                            4 *
                            reaction.R1 *
                            Math.pow(L1, 2)
                        )
                    );
            }

            /**
             * RIGHT SPAN
             */

            else {

                y =

                    (

                        (

                            reaction.R1 *
                            x / 6

                        ) *

                        (

                            Math.pow(x, 2) -

                            Math.pow(L1, 2)
                        )

                        +

                        (

                            reaction.R2 *
                            x / 6

                        ) *

                        (

                            Math.pow(x, 2) -

                            (
                                3 *
                                L1 *
                                x
                            ) +

                            (
                                3 *
                                Math.pow(L1, 2)
                            )
                        )

                        -

                        (

                            reaction.R2 *
                            Math.pow(L1, 3)

                        ) / 6

                        -

                        (

                            w *
                            x / 24

                        ) *

                        (

                            Math.pow(x, 3) -

                            Math.pow(L1, 3)
                        )

                    ) /

                    EI_CONVERTED;
            }

            /**
             * mm scale
             */

            y =
                y *
                1000 *
                j2;

            /**
             * downward negative
             */

            y = -Math.abs(y);

            /**
             * supports = zero
             */

            if (
                this.isEqual(x, 0) ||
                this.isEqual(x, L1) ||
                this.isEqual(x, total)
            ) {

                y = 0;
            }

            return {

                x,

                y:
                    Number(
                        y.toFixed(6)
                    )
            };
        };
    }

    /** =====================================================
     * CRITICAL POINTS
     * ===================================================== */

    getCriticalPoints(
        beam,
        load
    ) {

        const w =
            Number(load);

        const reaction =
            this.getReaction(
                beam,
                load
            );

        const total =
            beam.getTotalLength();

        return [

            0,

            beam.primarySpan,

            total,

            /**
             * zero shear left
             */

            reaction.R1 / w,

            /**
             * zero shear right
             */

            (
                reaction.R1 +
                reaction.R2
            ) / w

        ]
        .filter(v =>
            v >= 0 &&
            v <= total
        )
        .sort((a, b) => a - b);
    }
};

module.exports = {

    Material,
    Beam,
    BeamAnalysis
};