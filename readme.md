# CLT Supplier Management System

A Laravel-based application for managing suppliers, layups, and layers with full CRUD operations, export/import functionality, and conflict detection.

---

## 📋 Project Overview

This application provides a complete supplier management system with the following features:

- ✅ **Complete CRUD** - Manage Suppliers, Layups, and Layers with full functionality
- ✅ **Export & Import** - Export as JSON and import with conflict resolution
- ✅ **Conflict Detection** - Auto-detect conflicts with configurable resolution strategies
- ✅ **Dark Mode** - Beautiful dark-themed UI with Tailwind CSS
- ✅ **Production Ready** - Full validation, error handling, and responsive design

---

## 🚀 Installation & Setup

### Prerequisites

Ensure you have the following installed on your computer:

- **PHP 8.0 or higher**
- **Composer** (PHP package manager)
- **Node.js & npm** (for front-end assets)
- **MySQL/MariaDB** (or any supported database)
- **Git** (for cloning the repository)

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd candidate-test
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Setup Environment File

```bash
cp .env.example .env
```

Open `.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=candidate_test
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Run Database Migrations

```bash
php artisan migrate
```

### Step 6: Install Node Dependencies

```bash
npm install
```

### Step 7: Build Front-end Assets

```bash
npm run build
```

### Step 8: Create Sample User (Optional)

```bash
php artisan tinker
>>> \App\Models\User::factory()->create(['email' => 'test@example.com', 'password' => bcrypt('password')])
>>> exit
```

---

## 🏃 Running the Application

### Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

**Default Credentials:**
- Email: `test@example.com`
- Password: `password`

### Watch Mode (During Development)

In a separate terminal, run Vite in watch mode:

```bash
npm run dev
```

---

## 🗂️ Project Structure

```
├── app/
│   ├── Http/
│   │   └── Controllers/       # Application controllers
│   ├── Models/                # Database models (Supplier, Layup, Layer, User)
│   └── Services/              # Business logic (Export, Import, ConflictDetection)
├── database/
│   ├── migrations/            # Database schema
│   └── factories/             # Model factories for testing
├── resources/
│   ├── views/                 # Blade templates (suppliers, layups, layers)
│   └── css/                   # Tailwind CSS styles
├── routes/
│   └── web.php                # Application routes
└── tests/                     # Automated tests
```

---

## 🎯 Features & Usage

### 1. Manage Suppliers

- **Create**: Navigate to "Add Supplier" and enter supplier name
- **View**: Click on a supplier to see all layups and layers
- **Edit**: Update supplier name
- **Delete**: Remove supplier (with confirmation)

### 2. Manage Layups

- **Create**: From supplier view, click "Add Layup"
- **Edit**: Click edit button next to layup name
- **Delete**: Click delete button (removes all child layers)

### 3. Manage Layers

- **Create**: From layup section, click "Add Layer"
- **Edit**: Click edit icon in layer table
- **Delete**: Click delete icon (with confirmation)

**Layer Fields:**
- Layer Order (integer)
- Thickness (mm)
- Width (mm)
- Angle (0-360°)

### 4. Export Data

- From supplier view, click "Export" button
- Downloads JSON file containing supplier + all layups + all layers
- Can be used for backup or sharing

### 5. Import Data

- From suppliers list, click "Import" button
- Upload JSON file with the required format:

```json
{
  "name": "Supplier Name",
  "layups": [
    {
      "name": "Layup Name",
      "layers": [
        {
          "layer_order": 1,
          "thickness": 2.5,
          "width": 100,
          "angle": 45
        }
      ]
    }
  ]
}
```

- Conflicts are handled with "Skip" strategy (keeps existing data)

---

## 🛠️ Commands Reference

| Command | Description |
|---------|-------------|
| `php artisan serve` | Start development server |
| `php artisan migrate` | Run database migrations |
| `php artisan migrate:fresh` | Reset database and run migrations |
| `php artisan tinker` | Interactive PHP shell |
| `npm run dev` | Build assets in watch mode |
| `npm run build` | Build production assets |
| `php artisan test` | Run automated tests |
| `php artisan cache:clear` | Clear application cache |

---

## 📱 Responsive Design

The application is fully responsive and works on:
- ✅ Desktop (1024px and above)
- ✅ Tablet (768px - 1023px)
- ✅ Mobile (below 768px)

---

## 🎨 Dark Mode

The application uses a beautiful dark theme built with Tailwind CSS. Dark mode is enabled by default. To toggle, use your browser's theme settings or the settings page.

---

## 📝 Troubleshooting

### Database Connection Error

```
SQLSTATE[HY000]: General error: 1030 Got error 28 from storage engine
```

**Solution:**
1. Check `.env` database credentials
2. Ensure MySQL service is running
3. Create database manually: `CREATE DATABASE candidate_test;`

### Permission Denied

```
Permission denied: storage/logs/laravel.log
```

**Solution:**
```bash
chmod -R 775 storage bootstrap/cache
```

### Composer Dependencies Issue

```bash
composer install --no-dev
```

### NPM Build Error

```bash
rm -rf node_modules package-lock.json
npm install
npm run build
```

---

## 🔐 Security Notes

- ✅ CSRF protection enabled
- ✅ SQL injection prevention via prepared statements
- ✅ Input validation on all forms
- ✅ Authentication required for all operations
- ✅ Password hashing with bcrypt

---

## 📊 Database Schema

### Users Table
```
- id
- name
- email
- password
- created_at
- updated_at
```

### Suppliers Table
```
- id
- name
- created_at
- updated_at
```

### Layups Table
```
- id
- supplier_id (FK)
- name
- created_at
- updated_at
```

### Layers Table
```
- id
- layup_id (FK)
- layer_order
- thickness
- width
- angle
- created_at
- updated_at
```

---

## 🤝 Support

For issues or questions:

1. Check the troubleshooting section above
2. Review application logs in `storage/logs/`
3. Ensure all dependencies are correctly installed
4. Verify database connection

---

## 📄 License

This project is proprietary. All rights reserved.
# Feature Test Assignment

## 1. Instructions

- Clone or fork this repository.
- Create a new branch: `{user}-assignment`.
- Invite **@ikhsan017** and **@dhiaaziz** as collaborators.
- Follow the setup instructions provided in the repository before running the project.

## 2. Feature Requirements

### Core Features (Main Criteria)

- [ ] CRUD Suppliers
- [ ] CRUD CLT Layups (nested under Supplier)
- [ ] CRUD CLT Layers (nested under Layup)

The structure should properly reflect the hierarchy:
Supplier → Layups → Layers

### Data Model (ERD)

Below is the Entity Relationship Diagram (ERD) representing the data structure:

![ERD](./erd-new.png)

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
- **AND** one or more fields differ (`thickness`, `width`, `angle`),

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

- A modal, or
- A dedicated conflict resolution page.

## 4. Design Reference

A design reference is available in Figma:

[Figma Design File](https://www.figma.com/design/odWJ887r00aslmSFPIHMCx/SPEC-Toolbox---Feature-Test?node-id=11001-35&t=XUggOaUUi9p8jGFG-1)

> The design is for reference only. Exact visual matching is not required.

## 5. Evaluation Criteria

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

- Any meaningful enhancements will be considered positively

## 6. Submission

The deadline will be provided via email.  
Please ensure submission within the specified timeframe.


## 7. Demo

Include one of the following with your submission:

- A demo video (recommended), or
- A live project link

Ensure the demo clearly showcases:

- CRUD functionality
- Import / Export feature
- Conflict resolution behavior
