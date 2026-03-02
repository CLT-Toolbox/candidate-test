# Feature Test Assignment
# CLT Toolbox -- Candidate Technical Test Implementation
### Click to see Demo 👇
[![Demo](https://img.youtube.com/vi/kz4f-8Ic0MQ/hqdefault.jpg)](https://youtu.be/kz4f-8Ic0MQ)
------------------------------------------------------------------------

# Entity Relationship Diagram (ERD)

![ERD](erd-new.png)

------------------------------------------------------------------------

# Repository

Branch assignment:

azmigilar-gif-assignment

------------------------------------------------------------------------

# Clone Repository (Branch Assignment)

``` bash
git clone -b azmigilar-gif-assignment --single-branch https://github.com/azmigilar-gif/candidate-test.git
cd candidate-test
```

------------------------------------------------------------------------

# Setup Project

## 1. Install Dependencies

``` bash
composer install
npm install
```

------------------------------------------------------------------------

## 2. Environment Setup

``` bash
cp .env.example .env
php artisan key:generate
```

------------------------------------------------------------------------

## 3. Configure Database

Edit `.env`:

DB_DATABASE=your_database\
DB_USERNAME=your_username\
DB_PASSWORD=your_password

Run migration:

``` bash
php artisan migrate
```

Optional seeder:

``` bash
php artisan db:seed
```

------------------------------------------------------------------------

## 4. Run Application

Add script inside `package.json`:

``` json
"scripts": {
    "start": "concurrently \"php artisan serve\" \"npm run dev\""
}
```

Install concurrently if needed:

``` bash
npm install concurrently --save-dev
```

Run application:

``` bash
npm run start
```

Application will run at:

http://127.0.0.1:8000

------------------------------------------------------------------------

# Features Implemented

## Supplier Management

-   Create
-   Read
-   Update
-   Delete

## Layup Management

-   Nested under Supplier
-   CRUD
-   Validation
-   Unique layup handling

## Layer Management

-   Ordered layers
-   Validation for thickness, width, angle
-   Conflict detection based on `layer_order`

------------------------------------------------------------------------

# Export Feature

Supported formats: - JSON - CSV - XLSX

------------------------------------------------------------------------

# Import Feature

Supported formats: - JSON - CSV - XLSX

Import behavior: - Layup matched by `name` - Layer matched by
`layer_order` - Conflict detection before persistence

## Conflict Resolution Strategies

Skip\
Existing data remains unchanged.

Overwrite\
Existing data replaced with imported data.

Manual\
User compares existing vs incoming data before confirmation.

------------------------------------------------------------------------

# Architecture Approach

-   Supplier → Layup → Layer relational hierarchy
-   Service layer for business logic
-   Form Request validation
-   Clean import/export separation
-   Conflict resolution before DB transaction commit
-   Nested resource routing
