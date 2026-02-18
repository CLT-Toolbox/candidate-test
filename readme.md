# Feature Test Assignment

<<<<<<< HEAD
- Clone or fork this repository.
- Create a new branch, `{user}-assignment`, for your work.
- Invite `@ikhsan017` to be a collaborator.
- Please do migrate and seed first before running the project. After that `GET /api/suppliers` should be able to return a list of suppliers.
- The full brief about the project can be found here [Brief](https://honeysuckle-paneer-19b.notion.site/Skill-Test-Feature-Team-208eb56ecde880139e5dfcbdaa40ab4a).

### Feature List

- [ ] Authentication (Login, Register, Logout)
- [ ] CRUD Projects
- [ ] CRUD Building Part inside Projects
- [ ] User only sees their own projects and parts
=======
## 1. Instructions

- Clone or fork this repository.
- Create a new branch: `{user}-assignment`.
- Invite **@ikhsan017** and **@dhiaazizr** as collaborators.
- Follow the setup instructions provided in the repository before running the project.

## 2. Feature Requirements

### Core Features (Main Criteria)

- [ ] CRUD Suppliers
- [ ] CRUD CLT Layups (nested under Supplier)
- [ ] CRUD CLT Layers (nested under Layup)

The structure should properly reflect the hierarchy:
Supplier → Layups → Layers

### Import / Export (Main Criteria)

- [ ] **Export by Supplier**
    - Must include: Supplier + all related Layups + all related Layers

- [ ] **Import by Supplier**
    - Must create and/or update Layups and Layers under the specified supplier

Format is flexible (JSON / CSV / Excel, etc.). JSON format is completely acceptable.

## 3. Feature: Conflict Resolution (Bonus – Important)

During import, conflicts may occur when incoming data differs from existing records.

### Conflict Detection Rules

#### 1. Layup-Level Conflict

If a layup with the same `name` already exists under the same supplier:

- Treat it as the same layup candidate.
- Do **not** automatically create a new layup.

#### 2. Layer-Level Conflict

If:

- A layer with the same `layer_order` exists within that layup,
- **AND** one or more fields differ (`thickness`, `width`, `angle`, `grade_id`),

→ This must be treated as a conflict.

---

### Required Conflict Handling

You must implement a clearly defined conflict resolution strategy.

At minimum, support **one** of the following:

- **Overwrite Existing**  
  (Incoming data replaces current data)

- **Skip Conflict**  
  (Keep current data, ignore incoming change)

- **Duplicate Layup**  
  (Create a new layup with a suffix such as `name (imported)`)

- **Reject Entire Import**  
  (Abort and return a detailed conflict report)

---

### Advanced Conflict Resolution (UI-Based – Bonus)

For additional bonus points, implement a **manual conflict resolution interface** similar to GitHub merge conflict resolution.

Expected behavior:

- Display **Existing Version (Current Data)** and  
  **Incoming Version (Imported Data)** side-by-side
- Highlight field-level differences
- Allow the user to choose:
    - ✅ Keep Existing
    - ✅ Accept Incoming
- Support resolving conflicts one-by-one
- Provide navigation (e.g., “1 of 3 discrepancies”)

This may be implemented as:

- A modal,
- A dedicated conflict resolution page, or

---

### Additional Bonus Points

- Return a detailed import summary:
    - Layups created
    - Layups matched
    - Layers created
    - Layers updated
    - Layers skipped
    - Conflicts detected
- Support a `conflict_strategy` parameter on the import endpoint
>>>>>>> 527d322 (feat: update assignment)

Admin Only:
- [ ] Admin can access all projects and parts
- [ ] Analytical Dashboard

<<<<<<< HEAD
### Design File

- The design file is available at [Figma](https://www.figma.com/design/IjHne28E5eQNEA20FJK6bR/Skill-test--Timber-Sturctural-Project-Management-?node-id=0-1&p=f&t=oo1HxOY8229eVxSq-0). Please note that it is for reference only - there's no need to match the exact style.

### Main Criteria

- The feature list above is the main criteria for this assignment.

### Bonus Criteria

- Design Patterns (Repository, Service) =>
Use a Repository to abstract data access (e.g., `SupplierRepositoryInterface`) and/or a Service class to encapsulate business logic. Bind interfaces to implementations via a Service Provider.
=======
## 3. Design Reference

A design reference is available in Figma:

[Figma Design File](https://www.figma.com/design/odWJ887r00aslmSFPIHMCx/SPEC-Toolbox---Feature-Test?node-id=11001-35&t=XUggOaUUi9p8jGFG-1)
>>>>>>> 527d322 (feat: update assignment)

> The design is for reference only. Exact visual matching is not required.

## 4. Evaluation Criteria

### Main Evaluation

- Correct implementation of the required features

### Bonus Evaluation

**Architecture & Design Patterns**

- Use Repository and/or Service pattern
- Bind interfaces via a Service Provider

**Laravel Best Practices**

- Form Request validation
- Policies or Gates for authorization
- Proper use of Route Model Binding
- Clean, maintainable code following Laravel conventions

**Automated Testing**

- Unit tests (validation, services, repositories)
- Feature tests (CRUD and import/export flows)

**Additional Improvements**

- Implementing
- Any meaningful enhancements will be considered positively

## 5. Submission

The deadline will be provided via email.  
Please ensure submission within the specified timeframe.

<<<<<<< HEAD
### Submission

Please attach a demo video or a live project link here. (Update this section when you submit your assignment)
=======

## 6. Demo

Include one of the following with your submission:

- A demo video (recommended), or
- A live project link

Ensure the demo clearly showcases:

- CRUD functionality
- Import / Export feature
- Conflict resolution behavior
>>>>>>> 527d322 (feat: update assignment)
