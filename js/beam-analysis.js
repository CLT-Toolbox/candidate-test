"use strict";

/** ============================ Beam Analysis Data Type ============================ */

/**
 * Beam material specification.
 *
 * @param {String} name         Material name
 * @param {Object} properties   Material properties {EI : 0, j2: 0, ....}
 */
class Material {
  constructor(name, properties) {
    this.name = name;
    this.properties = properties;
  }
}

/**
 * @param {Number}   primarySpan     Beam primary span length (m)
 * @param {Number}   secondarySpan   Beam secondary span length (m)
 * @param {Material} material        Beam material object
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
      condition: "simply-supported",
    };

    this.analyzer = {
      "simply-supported": new BeamAnalysis.analyzer.simplySupported(),
      "two-span-unequal": new BeamAnalysis.analyzer.twoSpanUnequal(),
    };
  }

  /**
   * @param {Beam}   beam
   * @param {Number} load       UDL in kN/m
   * @param {String} condition
   */
  getDeflection(beam, load, condition) {
    var analyzer = this.analyzer[condition];
    if (analyzer) {
      return {
        beam: beam,
        load: load,
        equation: analyzer.getDeflectionEquation(beam, load),
      };
    }
    throw new Error("Invalid condition: " + condition);
  }

  getBendingMoment(beam, load, condition) {
    var analyzer = this.analyzer[condition];
    if (analyzer) {
      return {
        beam: beam,
        load: load,
        equation: analyzer.getBendingMomentEquation(beam, load),
      };
    }
    throw new Error("Invalid condition: " + condition);
  }

  getShearForce(beam, load, condition) {
    var analyzer = this.analyzer[condition];
    if (analyzer) {
      return {
        beam: beam,
        load: load,
        equation: analyzer.getShearForceEquation(beam, load),
      };
    }
    throw new Error("Invalid condition: " + condition);
  }
}

/** ============================ Beam Analysis Analyzer ============================ */

BeamAnalysis.analyzer = {};

/**
 * Simply Supported Beam — Uniformly Distributed Load (UDL)
 * ─────────────────────────────────────────────────────────
 * Boundary conditions : delta(0) = delta(L) = 0, M(0) = M(L) = 0
 *
 * Reactions   : RA = RB = w·L / 2
 * Shear       : V(x)  =  RA − w·x
 * Moment      : M(x)  =  RA·x − w·x²/2
 * Deflection  : δ(x)  = −(w·x / 24EI)·(L³ − 2L·x² + x³) · j2 · 1000  [mm]
 *
 * Notes
 *   • EI is supplied in N·mm²; divide by 1×10⁹ to get kN·m².
 *   • Multiply by j2 (long-term creep factor) and 1000 to yield mm.
 *
 * Reference: Excel sheet "1. Simply Supported UDL"
 */
BeamAnalysis.analyzer.simplySupported = class {
  constructor() {}

  getShearForceEquation(beam, load) {
    var w = load;
    var L = beam.primarySpan;
    var RA = (w * L) / 2;

    return function (x) {
      if (x < 0 || x > L) return { x: x, y: null };
      return { x: x, y: RA - w * x };
    };
  }

  getBendingMomentEquation(beam, load) {
    var w = load;
    var L = beam.primarySpan;
    var RA = (w * L) / 2;

    return function (x) {
      if (x < 0 || x > L) return { x: x, y: null };
      return { x: x, y: RA * x - (w * x * x) / 2 };
    };
  }

  getDeflectionEquation(beam, load) {
    var w = load;
    var L = beam.primarySpan;
    var EI = beam.material.properties.EI / 1e9; // N·mm² → kN·m²
    var j2 = beam.material.properties.j2 || 1;

    return function (x) {
      if (x < 0 || x > L) return { x: x, y: null };
      var delta =
        -((w * x) / (24 * EI)) *
        (L * L * L - 2 * L * x * x + x * x * x) *
        j2 *
        1000;
      return { x: x, y: delta };
    };
  }
};

/**
 * Two-Span Continuous Beam — Unequal Spans, Uniformly Distributed Load (UDL)
 * ───────────────────────────────────────────────────────────────────────────
 * Spans    : L1 (primary), L2 (secondary)
 * Supports : pin at x=0, roller at x=L1, pin at x=L1+L2
 *
 * Intermediate moment  (Three-Moment Theorem, M0 = M2 = 0):
 *   M1 = −(w/4) · (L1³ + L2³) / (2·(L1+L2))
 *
 * Reactions:
 *   R1 = w·L1/2 + M1/L1
 *   R3 = w·L2/2 + M1/L2
 *   R2 = w·(L1+L2) − R1 − R3
 *
 * Shear (0 to L1+L2):
 *   Span 1:  V(x) = R1 − w·x                          0  ≤ x ≤ L1
 *   Span 2:  V(x) = R1 + R2 − w·x                    L1 < x ≤ L1+L2
 *
 * Bending moment:
 *   Span 1:  M(x) = R1·x − w·x²/2                     0  ≤ x ≤ L1
 *   Span 2:  M(x) = R1·x + R2·(x−L1) − w·x²/2        L1 < x ≤ L1+L2
 *
 * Deflection — Span 1 (double integration, BC delta(0)=delta(L1)=0):
 *   δ₁(x) = (1/EI)·[R1·x³/6 − w·x⁴/24 + C1·x] · j2 · 1000  [mm]
 *   C1 = −R1·L1²/6 + w·L1³/24
 *
 * Deflection — Span 2 (superposition: UDL simply-supported + end moment M1):
 *   s = x − L1   (local, 0 ≤ s ≤ L2)
 *   δ_udl(s) = −w·s·(L2³ − 2·L2·s² + s³) / (24·EI)
 *   δ_M1(s)  =  M1·s·(L2² − s²) / (6·EI·L2)
 *   δ₂(s)    = (δ_udl + δ_M1) · j2 · 1000  [mm]
 *
 * Notes
 *   • EI in N·mm², divide by 1×10⁹ to get kN·m².
 *   • j2: long-term creep / load-duration factor.
 *
 * Reference: Excel sheet "2. Two unequal Span Equal UDL"
 */
BeamAnalysis.analyzer.twoSpanUnequal = class {
  constructor() {}

  /** Compute M1, R1, R2, R3 using Three-Moment Theorem */
  _reactions(beam, load) {
    var w = load;
    var L1 = beam.primarySpan;
    var L2 = beam.secondarySpan;

    var M1 = (-(w / 4) * (L1 * L1 * L1 + L2 * L2 * L2)) / (2 * (L1 + L2));
    var R1 = (w * L1) / 2 + M1 / L1;
    var R3 = (w * L2) / 2 + M1 / L2;
    var R2 = w * (L1 + L2) - R1 - R3;

    return { M1: M1, R1: R1, R2: R2, R3: R3 };
  }

  getShearForceEquation(beam, load) {
    var w = load;
    var L1 = beam.primarySpan;
    var L2 = beam.secondarySpan;
    var r = this._reactions(beam, load);

    return function (x) {
      if (x >= 0 && x <= L1) {
        return { x: x, y: r.R1 - w * x };
      } else if (x > L1 && x <= L1 + L2) {
        return { x: x, y: r.R1 + r.R2 - w * x };
      }
      return { x: x, y: null };
    };
  }

  getBendingMomentEquation(beam, load) {
    var w = load;
    var L1 = beam.primarySpan;
    var L2 = beam.secondarySpan;
    var r = this._reactions(beam, load);

    return function (x) {
      if (x >= 0 && x <= L1) {
        return { x: x, y: r.R1 * x - (w * x * x) / 2 };
      } else if (x > L1 && x <= L1 + L2) {
        return { x: x, y: r.R1 * x + r.R2 * (x - L1) - (w * x * x) / 2 };
      }
      return { x: x, y: null };
    };
  }

  getDeflectionEquation(beam, load) {
    var w = load;
    var L1 = beam.primarySpan;
    var L2 = beam.secondarySpan;
    var EI = beam.material.properties.EI / 1e9; // N·mm² → kN·m²
    var j2 = beam.material.properties.j2 || 1;
    var r = this._reactions(beam, load);

    // Span 1 integration constant (BC: delta(L1) = 0)
    var C1 = (-r.R1 * L1 * L1) / 6 + (w * L1 * L1 * L1) / 24;

    return function (x) {
      var delta;

      if (x >= 0 && x <= L1) {
        // Span 1 — double integration of moment diagram
        delta =
          (((r.R1 * Math.pow(x, 3)) / 6 - (w * Math.pow(x, 4)) / 24 + C1 * x) /
            EI) *
          j2 *
          1000;
      } else if (x > L1 && x <= L1 + L2) {
        // Span 2 — superposition (UDL + end moment M1 at intermediate support)
        var s = x - L1;
        var d_udl =
          -(w * s * (L2 * L2 * L2 - 2 * L2 * s * s + s * s * s)) / (24 * EI);
        var d_M1 = (r.M1 * s * (L2 * L2 - s * s)) / (6 * EI * L2);
        delta = (d_udl + d_M1) * j2 * 1000;
      } else {
        return { x: x, y: null };
      }

      return { x: x, y: delta };
    };
  }
};
