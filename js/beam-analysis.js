'use strict';

/** =========================================================
 * EXACT EXCEL VERSION
 * FINAL FIX (MATCH EXCEL TABLE + GRAPH)
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

    getShearForceEquation(
        beam,
        load
    ) {

        const L =
            beam.primarySpan;

        const w =
            Number(load);

        return function (x) {

            x = Number(x);

            const V =

                w *
                (
                    (L / 2) - x
                );

            return {

                x,

                y:
                    Number(
                        V.toFixed(6)
                    )
            };
        };
    }

    getBendingMomentEquation(
        beam,
        load
    ) {

        const L =
            beam.primarySpan;

        const w =
            Number(load);

        return function (x) {

            x = Number(x);

            /**
             * EXACT EXCEL
             * negative sagging
             */

            const M =

                -(
                    (
                        w *
                        x *
                        (
                            L - x
                        )
                    ) / 2
                );

            return {

                x,

                y:
                    Number(
                        M.toFixed(2)
                    )
            };
        };
    }

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

        return function (x) {

            x = Number(x);

            const y =

                -(
                    (
                        w *
                        x
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
                ) *

                j2 *

                1000;

            return {

                x,

                y:
                    Number(
                        y.toFixed(6)
                    )
            };
        };
    }
};

/** =========================================================
 * TWO UNEQUAL SPAN
 * ========================================================= */

BeamAnalysis.analyzer.twoSpanUnequal = class {

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

        /**
         * EXACT EXCEL
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
                8 *
                (
                    L1 + L2
                )
            );

        const R1 =

            (
                M1 / L1
            ) +

            (
                (
                    w * L1
                ) / 2
            );

        const R3 =

            (
                M1 / L2
            ) +

            (
                (
                    w * L2
                ) / 2
            );

        const R2 =

            (
                w * L1
            ) +

            (
                w * L2
            ) -

            R1 -

            R3;

        return {

            M1:
                Number(M1.toFixed(6)),

            R1:
                Number(R1.toFixed(6)),

            R2:
                Number(R2.toFixed(6)),

            R3:
                Number(R3.toFixed(6))
        };
    }

    /** =====================================================
     * SHEAR FORCE
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

        return function (x) {

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

            return {

                x,

                y:
                    Number(
                        V.toFixed(2)
                    )
            };
        };
    }

    /** =====================================================
     * BENDING MOMENT
     * EXACTLY MATCH EXCEL
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

        return function (x) {

            x = Number(x);

            let M = 0;

            /**
             * SUPPORTS
             */

            if (
                x === 0 ||
                x === total
            ) {

                M = 0;
            }

            /**
             * LEFT SPAN
             *
             * EXACT EXCEL:
             * =-((R1*x)-((w*x^2)/2))
             */

            else if (x < L1) {

                M =

                    -(
                        (
                            reaction.R1 * x
                        ) -

                        (
                            w *
                            Math.pow(x, 2) / 2
                        )
                    );
            }

            /**
             * MID SUPPORT
             *
             * EXACT EXCEL:
             * negative hogging
             */

            else if (x === L1) {

                M = reaction.M1;
            }

            /**
             * RIGHT SPAN
             *
             * EXACT EXCEL:
             * =-((R1*x)+(R2*(x-L1))-((w*x^2)/2))
             */

            else {

                M =

                    -(
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
                        )
                    );
            }

            return {

                x,

                y:
                    Number(
                        M.toFixed(2)
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

        return function (x) {

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
                    ) *

                    1000 *

                    j2;
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

                    EI_CONVERTED *

                    1000 *

                    j2;
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
};

module.exports = {
    Material,
    Beam,
    BeamAnalysis
};