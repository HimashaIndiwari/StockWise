# StockWise -- Product Inventory Management System

## Complete Implementation Plan for AI Coding Agents

**Project type:** Laravel web application\
**Assessment basis:** Apps Technologies -- Laravel Technical Evaluation
Task\
**Recommended stack:** Laravel, PHP, MySQL, Laravel Breeze, Blade,
Bootstrap 5, AdminLTE\
**UI direction:** Professional business dashboard using Navy + Emerald
Green + Light Grey\
**Document purpose:** A complete, phased implementation specification
that can be given to Claude Code, Antigravity, Cursor, or another coding
agent.

------------------------------------------------------------------------

## 1. Project objective

Build **StockWise**, a small but professional Product Inventory
Management System that demonstrates the Laravel concepts requested in
the technical evaluation:

-   Laravel application setup and application structure.
-   Authentication using Laravel Breeze, Jetstream, or Laravel UI. The
    selected implementation is **Laravel Breeze**.
-   MySQL database.
-   Two user roles: **Admin** and **Staff**.
-   Role-based access control using middleware.
-   Different dashboards after login.
-   Full CRUD for a core feature: Products.
-   Server-side validation for all forms.
-   Admin access to all records and user management.
-   Staff access restricted to their own records.
-   Bootstrap-based UI and proper navigation.
-   Clear routes in `routes/web.php`, route groups, named routes and
    appropriate HTTP methods.
-   Controllers for route logic; do not place business logic directly in
    route files.
-   Dashboard theme integrated through Laravel Blade layouts and
    partials.
-   Bonus features: search, pagination, policies/gates and profile
    management.
-   Public GitHub repository and setup documentation.

The additional inventory features in this plan are intended to make the
application coherent and useful. They must not delay completion of the
mandatory assessment requirements.

## 2. Assessment requirements and implementation mapping

  -----------------------------------------------------------------------
  Assessment requirement              Planned implementation
  ----------------------------------- -----------------------------------
  Laravel setup                       Laravel application using a
                                      PHP-compatible stable Laravel
                                      release

  Authentication scaffolding          Laravel Breeze

  MySQL or PostgreSQL                 MySQL

  Two user types                      Admin and Staff

  Role-based access control           Role middleware plus Laravel
                                      Policies

  Different dashboards                Admin dashboard and Staff dashboard

  Full CRUD                           Products: create, list, view,
                                      update and delete

  Form validation                     Laravel Form Request classes and
                                      server-side validation

  User-specific data                  Staff can access only records they
                                      own

  Admin permissions                   Admin can access all inventory
                                      records and manage users

  UI framework                        Bootstrap 5

  Routing                             Named routes, route groups and
                                      correct HTTP verbs

  Application structure               Laravel MVC

  Dashboard template                  AdminLTE or another suitable
                                      open-source Bootstrap template
                                      integrated with Blade

  Search and pagination               Product listing search, filters and
                                      pagination

  Policies or Gates                   Product Policy and appropriate
                                      authorization checks

  Profile management                  Update name, email and password

  Submission                          Public GitHub repository with
                                      README and `.env.example`
  -----------------------------------------------------------------------

**Important:** The assessment asks for a Laravel application. Do not
replace Laravel with Spring Boot, React, or another backend framework.

## 3. Scope and user roles

### 3.1 Admin

Admin is the system administrator and has full access to the
application.

Admin capabilities:

-   View the overall inventory dashboard.
-   View all products, including products created by every staff member.
-   Create, view, update and delete products.
-   Manage categories.
-   Manage suppliers.
-   Record and view stock movements.
-   View all users.
-   Create users and assign roles, subject to safe authorization rules.
-   Activate or deactivate staff accounts.
-   View inventory reports and low-stock products.
-   Manage their own profile.

### 3.2 Staff

Staff users have restricted access.

Staff capabilities:

-   View their own dashboard.
-   Create products.
-   View products they created.
-   Update their own products.
-   Delete their own products, subject to inventory-history safeguards.
-   Record stock IN and stock OUT for products they are authorized to
    manage.
-   View stock movement history for their own products.
-   Manage their own profile.

### 3.3 Mandatory authorization rules

-   Never rely on hiding a button as the security mechanism.
-   Enforce authorization on the server for every read, update and
    delete operation.
-   A staff user must not access another staff user's product by
    changing a URL or submitting a crafted request.
-   Admin can access all products and stock movement records.
-   Staff can access only their own products and related movements.
-   Protect admin-only routes with role middleware.
-   Use policies for record-level authorization.
-   Prevent staff from assigning themselves the Admin role.
-   Prevent accidental removal or deactivation of the last active
    administrator.
-   Validate that a product, category, supplier or user belongs to the
    permitted scope before performing an operation.

## 4. Technology stack

  -----------------------------------------------------------------------
  Technology                          Purpose
  ----------------------------------- -----------------------------------
  PHP                                 Backend language

  Laravel                             Backend framework and MVC structure

  MySQL                               Relational database

  Laravel Breeze                      Authentication scaffolding

  Blade                               Server-rendered UI templates

  Bootstrap 5                         CSS/UI framework

  AdminLTE                            Optional open-source dashboard
                                      template

  Eloquent ORM                        Models and relationships

  Laravel Middleware                  Authentication and role checks

  Laravel Policies                    Record-level authorization

  Form Request classes                Input validation

  JavaScript                          UI interactions and confirmation
                                      dialogs

  Vite                                Frontend asset bundling, if used by
                                      the selected Laravel setup

  Git and GitHub                      Version control and public
                                      submission

  PHPUnit or Pest                     Automated testing, according to the
                                      project setup
  -----------------------------------------------------------------------

Choose a stable Laravel release compatible with the installed PHP
version and the selected Breeze version. The evaluation document does
not prescribe a specific Laravel version. Do not upgrade or downgrade
framework dependencies without checking compatibility.

## 5. Visual design system and color palette

### 5.1 Design direction

Use a clean, professional business dashboard. The design should be
consistent across login, dashboards, product pages, category pages,
supplier pages, stock pages, user management and profile pages.

**Theme:** Navy + Emerald Green + Light Grey.

### 5.2 Color tokens

  -----------------------------------------------------------------------
  Token                   Hex code                Usage
  ----------------------- ----------------------- -----------------------
  Primary Navy            `#172B4D`               Primary buttons, key
                                                  headings and brand
                                                  emphasis

  Sidebar Navy            `#243B53`               Sidebar background

  Emerald Green           `#16A36A`               Success actions,
                                                  positive stock changes
                                                  and primary success
                                                  indicators

  Success Background      `#E9F7EF`               Success alerts and
                                                  positive status
                                                  backgrounds

  Main Background         `#F4F6F9`               Main application page
                                                  background

  Surface White           `#FFFFFF`               Cards, tables, forms
                                                  and content panels

  Border Grey             `#E5E7EB`               Borders and dividers

  Main Text               `#334155`               Primary text

  Secondary Text          `#64748B`               Helper text and
                                                  secondary labels

  Warning Orange          `#F59E0B`               Low-stock warnings

  Danger Red              `#DC4C4C`               Delete actions, errors
                                                  and out-of-stock
                                                  warnings
  -----------------------------------------------------------------------

### 5.3 UI rules

-   Use a dark navy sidebar with readable white labels and icons.
-   Use a white top navigation bar and white content cards.
-   Use the light grey page background.
-   Use emerald for successful actions and positive stock status.
-   Use orange for low-stock alerts.
-   Use red for destructive actions and out-of-stock status.
-   Use consistent typography, spacing, borders, icon sizes and button
    heights.
-   Use consistent status badges.
-   Provide visible validation errors beside the relevant fields.
-   Provide success and failure flash messages.
-   Include confirmation dialogs for destructive actions.
-   Include empty states when tables have no records.
-   Ensure responsive behavior on desktop, tablet and mobile.
-   Maintain readable contrast. Do not use color alone to communicate
    status.

### 5.4 Main layout

Create reusable Blade layouts and partials:

-   `resources/views/layouts/app.blade.php` -- authenticated application
    shell.
-   `resources/views/layouts/guest.blade.php` -- guest/authentication
    shell.
-   `resources/views/partials/sidebar.blade.php`.
-   `resources/views/partials/navbar.blade.php`.
-   `resources/views/partials/footer.blade.php`.
-   `resources/views/partials/flash-messages.blade.php`.
-   Reusable Blade components for buttons, status badges, form fields
    and confirmation UI where appropriate.

Integrate AdminLTE or another suitable Bootstrap dashboard template
properly through Blade layouts and partials. Do not copy a theme into
every page separately. Ensure login, dashboard and CRUD views all have a
consistent design.

## 6. Functional modules

### Module 1 -- Authentication

Implement using Laravel Breeze.

Features:

-   Login.
-   Logout.
-   Registration only if the chosen system setup requires it; if public
    registration is enabled, newly registered users must never become
    Admin automatically.
-   Password reset.
-   Authenticated route protection.
-   Role-aware login redirect.
-   Profile management.
-   Secure password hashing using Laravel defaults.

After successful login:

-   Admin goes to the Admin dashboard.
-   Staff goes to the Staff dashboard.

Use the authentication features supplied by Breeze rather than creating
a separate, insecure authentication system.

### Module 2 -- Dashboard

#### Admin dashboard

Display data across the entire inventory:

-   Total products.
-   Total categories.
-   Total suppliers.
-   Total users.
-   Total stock quantity.
-   Low-stock product count.
-   Out-of-stock product count.
-   Recent stock movements.
-   Useful stock summary or chart, if time permits.

#### Staff dashboard

Display only data within the logged-in staff user's permitted scope:

-   My total products.
-   My total stock quantity.
-   My low-stock products.
-   My out-of-stock products.
-   My recent stock movements.

Dashboard counts must be calculated from the database and must respect
authorization and ownership rules. Do not hardcode statistics.

### Module 3 -- Product management (core CRUD)

This is the most important feature for the assessment.

#### Create product

Fields:

-   Product name.
-   SKU / product code.
-   Description.
-   Category.
-   Supplier (optional).
-   Purchase price.
-   Selling price.
-   Initial quantity.
-   Minimum stock level.
-   Product image (optional).
-   Creator (`created_by`, assigned automatically from the authenticated
    user).

#### Read products

Provide:

-   Product listing table.
-   Product details page.
-   SKU.
-   Category.
-   Supplier.
-   Purchase price and selling price, where permitted.
-   Available quantity.
-   Minimum stock level.
-   Stock status.
-   Creator and created date for Admin.
-   Search, filters, sorting and pagination.

#### Update product

Allow authorized users to update permitted product details. Validate all
updated fields. Do not let a normal product edit silently change stock
quantity without creating an auditable stock movement.

#### Delete product

-   Show a confirmation dialog.
-   Use Laravel SoftDeletes for products.
-   Preserve stock movement history.
-   Do not physically delete a product that has stock movement history.
-   Decide and implement a clear rule for whether products with
    remaining stock can be archived; preferably require stock to be zero
    before archival/deletion.
-   Hide soft-deleted products from normal listings.
-   Admin may have a separate archived-products view if time permits.

#### Product rules

-   SKU must be unique.
-   Product name is required.
-   Category must exist.
-   Supplier, when supplied, must exist.
-   Prices must be valid non-negative decimal values.
-   Quantity and minimum stock must be non-negative integers.
-   Creator must be assigned on the server, not trusted from the form.
-   Staff ownership must be checked by policy.
-   Use decimal database types for prices, not floating-point types.

### Module 4 -- Category management

Features:

-   Create category.
-   List categories.
-   View category details if useful.
-   Update category.
-   Delete categories only when they are not assigned to products.
-   Validate category names and prevent duplicates.
-   Use confirmation before deletion.

Suggested fields:

-   `id`
-   `name`
-   `description`
-   `created_at`
-   `updated_at`

Admin manages all categories. Staff may use categories when creating
products but should not manage category records unless explicitly
authorized.

### Module 5 -- Supplier management

Features:

-   Create supplier.
-   List suppliers.
-   View supplier details.
-   Update supplier.
-   Delete suppliers only when they are not assigned to products.
-   Search suppliers.
-   Validate contact information.

Suggested fields:

-   `id`
-   `name`
-   `email` (nullable)
-   `phone` (nullable)
-   `address` (nullable)
-   `created_at`
-   `updated_at`

Admin manages suppliers. Staff may select an existing supplier when
creating a product.

### Module 6 -- Stock management

Implement stock movement as a separate auditable feature.

Movement types:

-   **IN:** Adds stock.
-   **OUT:** Removes stock.
-   **ADJUSTMENT:** Corrects stock after a count or correction, with a
    reason.

Each movement should record:

-   Product.
-   User who performed the movement.
-   Movement type.
-   Quantity changed.
-   Previous quantity.
-   New quantity.
-   Reason or notes.
-   Date and time.

Example:

  Movement       Quantity change   Previous stock   New stock
  ------------ ----------------- ---------------- -----------
  IN                         +20               50          70
  OUT                        -10               70          60
  ADJUSTMENT                  -5               60          55

Business rules:

-   Never allow negative stock.
-   Stock OUT must not exceed available stock.
-   Quantity must be a positive integer for IN and OUT.
-   An adjustment must result in a non-negative quantity.
-   Require a reason for adjustments.
-   Every successful stock change must create a stock movement record.
-   Record the authenticated user automatically.
-   Do not allow ordinary product editing to bypass the stock movement
    history.
-   Use a database transaction for the product quantity update and
    movement insert.
-   Use row locking (`lockForUpdate`) inside the transaction when
    appropriate to prevent concurrent stock operations from corrupting
    quantities.
-   If any part of the operation fails, roll back both the quantity
    update and movement insert.
-   Preserve movement history when a product is archived.

Implement stock calculation logic in a dedicated `StockService`, rather
than duplicating it in controllers.

### Module 7 -- User management

Admin-only features:

-   List users.
-   View user details.
-   Create a staff user.
-   Update permitted user details.
-   Assign roles under strict Admin authorization.
-   Activate or deactivate staff accounts.
-   Search and paginate users.

Security rules:

-   Staff cannot access user management.
-   Staff cannot assign roles.
-   Never expose password hashes.
-   Do not allow an Admin to accidentally remove or deactivate the last
    active Admin.
-   Protect role changes with authorization and validation.
-   Do not allow public registration to create an Admin account.

### Module 8 -- Search, filters and reports

Bonus and additional features:

-   Search products by name or SKU.
-   Filter by category.
-   Filter by supplier.
-   Filter by stock status.
-   Sort by name, price, quantity or creation date.
-   Paginate product listings.
-   Preserve active search/filter query parameters when moving between
    pages.
-   Low-stock report.
-   Out-of-stock report.
-   Stock movement report.
-   Summary of stock IN and OUT.

Do not add complicated export functionality until the mandatory features
and tests are complete.

### Module 9 -- Profile management

Use the Laravel profile functionality where available and adapt it to
the selected UI.

Features:

-   Update name.
-   Update email.
-   Change password.
-   Validate current password when changing password.
-   Validate unique email.
-   Do not allow users to edit their own role through profile settings.

## 7. Database design

Use MySQL and Laravel migrations. Use Eloquent models and relationships.

### 7.1 `users` table

Laravel Breeze may already create this table. Inspect the existing
migration and extend it rather than creating a duplicate.

  -----------------------------------------------------------------------
  Column                  Suggested type          Rules / purpose
  ----------------------- ----------------------- -----------------------
  `id`                    BIGINT UNSIGNED         Primary key

  `name`                  VARCHAR(255)            Required

  `email`                 VARCHAR(255)            Required, unique

  `email_verified_at`     TIMESTAMP nullable      Laravel verification
                                                  support if used

  `password`              VARCHAR(255)            Required, hashed

  `role`                  VARCHAR(20) or suitable `admin` or `staff`;
                          string                  default `staff`

  `status`                VARCHAR(20) or suitable `active` or `inactive`;
                          string                  default `active`

  `remember_token`        VARCHAR(100) nullable   Laravel authentication

  `created_at`            TIMESTAMP nullable      Laravel timestamp

  `updated_at`            TIMESTAMP nullable      Laravel timestamp
  -----------------------------------------------------------------------

Use a simple role column for this small evaluation project. A separate
permission package is not necessary.

### 7.2 `categories` table

  Column          Suggested type       Rules / purpose
  --------------- -------------------- -------------------
  `id`            BIGINT UNSIGNED      Primary key
  `name`          VARCHAR(255)         Required; unique
  `description`   TEXT nullable        Optional
  `created_at`    TIMESTAMP nullable   Laravel timestamp
  `updated_at`    TIMESTAMP nullable   Laravel timestamp

### 7.3 `suppliers` table

  Column         Suggested type          Rules / purpose
  -------------- ----------------------- --------------------------------
  `id`           BIGINT UNSIGNED         Primary key
  `name`         VARCHAR(255)            Required
  `email`        VARCHAR(255) nullable   Optional; validate if supplied
  `phone`        VARCHAR(50) nullable    Optional
  `address`      TEXT nullable           Optional
  `created_at`   TIMESTAMP nullable      Laravel timestamp
  `updated_at`   TIMESTAMP nullable      Laravel timestamp

### 7.4 `products` table

  -----------------------------------------------------------------------
  Column                  Suggested type          Rules / purpose
  ----------------------- ----------------------- -----------------------
  `id`                    BIGINT UNSIGNED         Primary key

  `name`                  VARCHAR(255)            Required

  `sku`                   VARCHAR(100)            Required; unique

  `description`           TEXT nullable           Optional

  `category_id`           BIGINT UNSIGNED         Required foreign key to
                                                  categories

  `supplier_id`           BIGINT UNSIGNED         Optional foreign key to
                          nullable                suppliers

  `created_by`            BIGINT UNSIGNED         Required foreign key to
                                                  users

  `purchase_price`        DECIMAL(12,2)           Required; non-negative

  `selling_price`         DECIMAL(12,2)           Required; non-negative

  `quantity`              INT UNSIGNED            Required; default 0

  `min_stock_level`       INT UNSIGNED            Required; default 5 or
                                                  agreed seed/default

  `image`                 VARCHAR(255) nullable   Optional stored image
                                                  path

  `created_at`            TIMESTAMP nullable      Laravel timestamp

  `updated_at`            TIMESTAMP nullable      Laravel timestamp

  `deleted_at`            TIMESTAMP nullable      SoftDeletes
  -----------------------------------------------------------------------

Add indexes for commonly filtered fields, such as `category_id`,
`supplier_id`, `created_by` and stock quantity where appropriate. SKU
must have a unique index.

### 7.5 `stock_movements` table

  -----------------------------------------------------------------------
  Column                  Suggested type          Rules / purpose
  ----------------------- ----------------------- -----------------------
  `id`                    BIGINT UNSIGNED         Primary key

  `product_id`            BIGINT UNSIGNED         Required foreign key to
                                                  products

  `user_id`               BIGINT UNSIGNED         Required foreign key to
                                                  users

  `type`                  VARCHAR(20)             `IN`, `OUT` or
                                                  `ADJUSTMENT`

  `quantity`              INT UNSIGNED            Positive quantity
                                                  difference

  `previous_quantity`     INT UNSIGNED            Quantity before
                                                  operation

  `new_quantity`          INT UNSIGNED            Quantity after
                                                  operation

  `reason`                TEXT nullable           Notes; required for
                                                  adjustment

  `created_at`            TIMESTAMP nullable      Movement timestamp

  `updated_at`            TIMESTAMP nullable      Laravel timestamp if
                                                  standard timestamps are
                                                  used
  -----------------------------------------------------------------------

The movement row is an audit record. Do not edit or delete historical
movements through ordinary CRUD screens. If corrections are needed,
create a new adjustment movement.

### 7.6 Laravel support tables

Keep the support migrations supplied by the chosen Laravel/Breeze setup
where required, such as:

-   `password_reset_tokens`
-   `sessions` when database sessions are configured.
-   Any other framework-required tables already present in the project.

Do not recreate tables that Laravel has already generated.

### 7.7 Foreign keys and deletion behavior

-   Product → Category: required relationship; prevent category deletion
    while products reference it.
-   Product → Supplier: optional relationship; prevent supplier deletion
    while products reference it, or use a carefully designed nullable
    relationship if the business rule explicitly permits reassignment.
-   Product → User (`created_by`): preserve creator history. Do not
    cascade-delete products when a user is removed.
-   StockMovement → Product: preserve history. Product soft deletion
    should not delete movements.
-   StockMovement → User: preserve the responsible user's audit
    identity; avoid cascading deletion of movement history.
-   Use restrictive foreign-key behavior where history must be
    preserved.
-   Use soft deletion for products.
-   Categories and suppliers should not be hard-deleted while in use.

### 7.8 Eloquent relationships

-   `User` hasMany `Product` through `created_by`.
-   `User` hasMany `StockMovement` through `user_id`.
-   `Category` hasMany `Product`.
-   `Supplier` hasMany `Product`.
-   `Product` belongsTo `Category`.
-   `Product` belongsTo `Supplier`.
-   `Product` belongsTo `User` as creator.
-   `Product` hasMany `StockMovement`.
-   `StockMovement` belongsTo `Product`.
-   `StockMovement` belongsTo `User`.

Use explicit relationship method names, especially for the product
creator relationship, to avoid ambiguity.

### 7.9 Seeders

Create development seeders for:

-   One Admin account.
-   At least two Staff accounts.
-   A few categories.
-   A few suppliers.
-   Several sample products owned by different staff members.
-   A small set of sample stock movements if useful.

Use safe local-development credentials and document them in the README.
Do not commit real production credentials. Ensure seeders do not
overwrite or weaken an existing production database.

## 8. Laravel architecture and folder structure

Use Laravel MVC and keep responsibilities separated.

Suggested structure (adapt to the actual Laravel version and existing
project):

``` text
stockwise/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   ├── Staff/
│   │   │   ├── ProductController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── SupplierController.php
│   │   │   ├── StockMovementController.php
│   │   │   ├── DashboardController.php
│   │   │   └── UserManagementController.php
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/
│   │       ├── StoreProductRequest.php
│   │       ├── UpdateProductRequest.php
│   │       ├── StoreCategoryRequest.php
│   │       ├── StoreSupplierRequest.php
│   │       └── StoreStockMovementRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Supplier.php
│   │   └── StockMovement.php
│   ├── Policies/
│   │   └── ProductPolicy.php
│   └── Services/
│       └── StockService.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── partials/
│       ├── components/
│       ├── auth/
│       ├── admin/
│       ├── staff/
│       ├── products/
│       ├── categories/
│       ├── suppliers/
│       ├── stock/
│       ├── users/
│       └── profile/
├── routes/
│   └── web.php
├── tests/
│   ├── Feature/
│   └── Unit/
└── README.md
```

Do not create unnecessary folders or duplicate existing Laravel files.
Follow conventions of the Laravel version already installed.

### Architecture responsibilities

-   **Routes:** Declare URL patterns, middleware, names and controller
    actions.
-   **Middleware:** Authentication and role-level access.
-   **Controllers:** Receive requests and coordinate application
    operations.
-   **Form Requests:** Validate incoming form data and authorize where
    appropriate.
-   **Policies:** Check whether a user may view, update or delete a
    specific record.
-   **Services:** Encapsulate multi-step business logic, especially
    stock operations.
-   **Models:** Define relationships, casts, scopes and model-level
    behavior.
-   **Blade:** Render pages and reusable UI components.
-   **Migrations:** Define database schema and constraints.
-   **Tests:** Verify expected behavior and security boundaries.

Do not put business logic directly in `routes/web.php`. Avoid large
controllers and duplicated stock calculations.

## 9. Routing plan

Use named routes and grouped routes in `routes/web.php`. Route names and
prefixes should be consistent.

Illustrative route organization:

``` php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Shared profile routes
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);

        // Admin user management
        // Admin stock movement listing
        // Admin reports
    });

Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        // Staff dashboard
        // Staff-owned product routes
        // Authorized stock movement routes
    });
```

This is a structural example. Adapt controller namespaces, route names
and middleware registration to the installed Laravel version.

Product resource routes should provide:

  HTTP method   Resource action   Purpose
  ------------- ----------------- -----------------------------
  GET           index             List products
  GET           create            Display create form
  POST          store             Save product
  GET           show              Display product
  GET           edit              Display edit form
  PUT/PATCH     update            Update product
  DELETE        destroy           Soft-delete/archive product

Use appropriate HTTP verbs, named routes and route model binding. Do not
assume that route grouping alone provides record-level ownership
security.

## 10. Validation specification

Use Laravel Form Request classes for create/update operations and
validate all user input on the server.

### Product validation

  -----------------------------------------------------------------------
  Field                               Suggested validation
  ----------------------------------- -----------------------------------
  `name`                              required, string, max:255

  `sku`                               required, string, max:100, unique;
                                      ignore current product on update

  `description`                       nullable, string

  `category_id`                       required, exists:categories,id

  `supplier_id`                       nullable, exists:suppliers,id

  `purchase_price`                    required, numeric, min:0, decimal
                                      precision appropriate to schema

  `selling_price`                     required, numeric, min:0, decimal
                                      precision appropriate to schema

  `quantity`                          required on creation if entered,
                                      integer, min:0; do not update
                                      through ordinary edit after stock
                                      history begins

  `min_stock_level`                   required, integer, min:0

  `image`                             nullable, image, allowed MIME
                                      types, reasonable maximum size
  -----------------------------------------------------------------------

Additional rules:

-   If the business rule requires selling price to be at least purchase
    price, validate it server-side and explain the rule in the UI.
-   Never accept `created_by` from user input.
-   Never accept role changes through a staff profile form.
-   Verify that referenced category and supplier records are
    active/usable.
-   Preserve the old image or safely remove it when a replacement is
    uploaded; do not leave orphaned files unnecessarily.

### Category validation

-   Name: required, string, maximum length, unique.
-   Description: nullable string.
-   Prevent deletion while products reference the category.

### Supplier validation

-   Name: required, string, maximum length.
-   Email: nullable, valid email.
-   Phone: nullable, reasonable maximum length.
-   Address: nullable string.
-   Prevent deletion while products reference the supplier.

### Stock movement validation

-   Product: required and authorized for the current user.
-   Type: required and one of `IN`, `OUT`, `ADJUSTMENT`.
-   Quantity: positive integer.
-   Reason: required for adjustments.
-   Check available stock inside the transaction, not only in a
    pre-validation check.
-   Do not trust user ID, previous quantity or new quantity from the
    client. Calculate/assign them on the server.

## 11. Stock transaction algorithm

Implement the following logic in `StockService`:

1.  Begin a database transaction.
2.  Load the selected product with a row lock (`lockForUpdate`) inside
    the transaction.
3.  Verify the current user is authorized to manage the product.
4.  Read the current quantity from the locked product row.
5.  Validate the movement type and quantity.
6.  Calculate the new quantity:
    -   IN: previous quantity + movement quantity.
    -   OUT: previous quantity - movement quantity.
    -   ADJUSTMENT: set the new quantity according to the approved
        adjustment input and record the absolute difference as the
        movement quantity.
7.  Reject any operation that would make stock negative.
8.  Update the product quantity.
9.  Insert the stock movement with product, user, type, quantity
    difference, previous quantity, new quantity and reason.
10. Commit the transaction.
11. If any operation fails, roll back the transaction and show a safe
    error message.

Do not implement stock changes as separate unprotected update and insert
operations. The product quantity and movement history must remain
consistent.

## 12. Dashboard and navigation page specification

### Admin navigation

-   Dashboard
-   Products
-   Categories
-   Suppliers
-   Stock Movements
-   User Management
-   Reports
-   My Profile
-   Logout

### Staff navigation

-   Dashboard
-   My Products
-   Stock Movements
-   My Profile
-   Logout

Do not show admin-only navigation to staff. More importantly, enforce
access on the server too.

### Suggested pages

  Page              Example URL
  ----------------- ----------------------------------
  Login             `/login`
  Admin dashboard   `/admin/dashboard`
  Staff dashboard   `/staff/dashboard`
  Admin products    `/admin/products`
  Create product    `/admin/products/create`
  Edit product      `/admin/products/{product}/edit`
  Product details   `/admin/products/{product}`
  Categories        `/admin/categories`
  Suppliers         `/admin/suppliers`
  Stock movements   `/admin/stock-movements`
  User management   `/admin/users`
  Profile           `/profile`

Use the actual named routes to generate links rather than hardcoding
URLs in Blade templates.

## 13. Search, pagination and stock status

### Product search

-   Search by product name or SKU.
-   Filter by category.
-   Filter by supplier.
-   Filter by stock status.
-   Sort by name, price, quantity and date.
-   Use database queries, not loading every product into memory.
-   Paginate results.
-   Preserve query parameters when navigating pages.
-   Apply the current user's ownership scope for Staff.

### Stock status calculation

Use a consistent definition:

-   **Out of stock:** quantity equals 0.
-   **Low stock:** quantity is greater than 0 and less than or equal to
    minimum stock level.
-   **In stock:** quantity is greater than minimum stock level.

Use the same definition in product tables, dashboard cards and reports.

## 14. Testing and quality assurance

Create automated tests using the testing framework already configured by
Laravel.

### Authentication tests

-   Guest cannot access authenticated pages.
-   Valid Admin login succeeds.
-   Valid Staff login succeeds.
-   Admin redirects to Admin dashboard.
-   Staff redirects to Staff dashboard.
-   Inactive users cannot access protected pages.

### Role and authorization tests

-   Staff cannot access Admin routes.
-   Admin can access all products.
-   Staff can see their own products.
-   Staff cannot view another user's product.
-   Staff cannot update another user's product.
-   Staff cannot delete another user's product.
-   Staff cannot change their role.
-   Unauthorized direct requests are rejected.

### Product CRUD tests

-   Create valid product.
-   Reject missing required fields.
-   Reject duplicate SKU.
-   Reject invalid category.
-   Reject invalid supplier.
-   Update valid product.
-   Reject invalid updates.
-   Soft-delete authorized product.
-   Verify deleted products do not appear in normal listings.

### Stock movement tests

-   Stock IN increases quantity.
-   Stock OUT decreases quantity.
-   Stock OUT greater than available quantity is rejected.
-   Stock never becomes negative.
-   Adjustment requires a reason.
-   Every successful movement creates a history record.
-   Movement stores the correct previous and new quantities.
-   Failed movement leaves both product quantity and movement history
    unchanged.
-   Unauthorized users cannot record movements for another user's
    product.

### UI and regression checks

-   Navigation links work.
-   Forms show validation errors.
-   Success/error messages display.
-   Empty states display.
-   Search and filters work.
-   Pagination works.
-   Layout is responsive.
-   Login and CRUD pages use the selected theme consistently.

Run the existing test suite after each major implementation phase. Do
not claim tests passed unless they were actually run and the output was
checked.

## 15. Development roadmap -- phase by phase

Complete the project in this order. Do not begin advanced features while
mandatory requirements are incomplete.

### Phase 1 -- Repository inspection and project setup

Tasks:

1.  Inspect the current repository and identify whether it is empty or
    contains an existing Laravel application.
2.  Check PHP, Composer, Laravel, Node and npm versions.
3.  Check the existing Git branch and working tree.
4.  Check existing environment and database configuration without
    exposing secrets.
5.  Select a compatible Laravel release.
6.  Create the Laravel application only if one does not already exist.
7.  Configure MySQL.
8.  Install Laravel Breeze.
9.  Confirm login and logout work.
10. Initialize or preserve Git safely.

**Deliverable:** Running Laravel application with working
authentication.

**Acceptance criteria:** Application starts, connects to the intended
development database and authenticates a test user.

### Phase 2 -- Database and Eloquent relationships

Tasks:

1.  Inspect existing migrations.
2.  Extend the Breeze users migration with role and status fields.
3.  Create categories migration.
4.  Create suppliers migration.
5.  Create products migration.
6.  Create stock movements migration.
7.  Add foreign keys, unique constraints and indexes.
8.  Add SoftDeletes to products.
9.  Create/update Eloquent models.
10. Implement relationships.
11. Create factories and seeders.
12. Run migrations against the explicitly configured development
    database.
13. Verify the resulting schema and seeded records.

**Deliverable:** Database schema and working relationships.

**Acceptance criteria:** Migrations run successfully; foreign keys and
relationships work; development seed data is available.

### Phase 3 -- Authentication, roles and authorization

Tasks:

1.  Add Admin and Staff role handling.
2.  Implement role middleware.
3.  Register middleware using the correct mechanism for the Laravel
    version.
4.  Configure role-aware dashboard redirects.
5.  Create Admin and Staff dashboard routes.
6.  Implement Product Policy.
7.  Enforce product ownership.
8.  Protect Admin-only routes.
9.  Protect user management.
10. Add tests for unauthorized access.

**Deliverable:** Secure two-role application.

**Acceptance criteria:** Admin and Staff see the correct dashboards;
unauthorized direct requests are rejected.

### Phase 4 -- Product CRUD

Tasks:

1.  Implement ProductController.
2.  Implement StoreProductRequest.
3.  Implement UpdateProductRequest.
4.  Implement product index/listing.
5.  Implement create form and store action.
6.  Implement details page.
7.  Implement edit form and update action.
8.  Implement soft-delete/archive behavior.
9.  Add server-side validation.
10. Add ownership policies.
11. Add confirmation dialogs and flash messages.
12. Add product CRUD feature tests.

**Deliverable:** Complete product CRUD.

**Acceptance criteria:** Admin can manage all products; Staff can manage
only their own; all form inputs are validated.

### Phase 5 -- Category and supplier management

Tasks:

1.  Implement Category model/controller/requests/views.
2.  Implement category CRUD.
3.  Prevent deletion of categories in use.
4.  Implement Supplier model/controller/requests/views.
5.  Implement supplier CRUD.
6.  Prevent deletion of suppliers in use.
7.  Add search/pagination if appropriate.
8.  Add authorization and tests.

**Deliverable:** Category and supplier modules.

**Acceptance criteria:** Authorized users can manage reference data
without breaking product relationships.

### Phase 6 -- Stock management

Tasks:

1.  Implement StockMovement model and relationships.
2.  Implement StockService.
3.  Implement stock IN.
4.  Implement stock OUT.
5.  Implement stock adjustment with reason.
6.  Use database transactions and row locking.
7.  Store previous and new quantity.
8.  Record the authenticated user.
9.  Implement movement history.
10. Add stock validation and tests.

**Deliverable:** Reliable stock management with audit history.

**Acceptance criteria:** Stock never becomes negative and each
successful change has a matching movement record.

### Phase 7 -- Dashboards and reports

Tasks:

1.  Build Admin dashboard statistics.
2.  Build Staff dashboard statistics.
3.  Add low-stock and out-of-stock indicators.
4.  Add recent movements.
5.  Add product search.
6.  Add filters and sorting.
7.  Add pagination.
8.  Add basic stock reports.
9.  Verify all dashboard queries respect role and ownership.

**Deliverable:** Useful role-specific dashboards and inventory overview.

**Acceptance criteria:** All figures are database-driven and staff
cannot see another user's private records.

### Phase 8 -- UI/UX and dashboard theme

Tasks:

1.  Select and integrate AdminLTE or a suitable open-source Bootstrap
    theme.
2.  Create guest and authenticated Blade layouts.
3.  Create reusable sidebar and navbar partials.
4.  Apply the specified color palette.
5.  Style login and authentication pages.
6.  Style dashboards.
7.  Style product listing and forms.
8.  Style categories, suppliers, stock, users and profile pages.
9.  Add consistent buttons, badges, tables and form controls.
10. Add responsive layouts.
11. Add validation, flash messages, empty states and confirmation
    dialogs.
12. Review accessibility and contrast.

**Deliverable:** Consistent professional UI.

**Acceptance criteria:** Every application page uses the same visual
design system and works at common screen sizes.

### Phase 9 -- Profile management and optional bonuses

Tasks:

1.  Update profile name.
2.  Update profile email with uniqueness validation.
3.  Implement password change.
4.  Validate current password.
5.  Prevent profile role escalation.
6.  Verify policies and gates are used where needed.
7.  Complete search and pagination.
8.  Review optional features against available time.

**Deliverable:** Completed bonus features.

**Acceptance criteria:** Profile changes work securely and optional
assessment features are demonstrated.

### Phase 10 -- Full testing, security and regression

Tasks:

1.  Run all automated tests.
2.  Test Admin workflows.
3.  Test Staff workflows.
4.  Test direct URL access.
5.  Test crafted unauthorized requests.
6.  Test validation failures.
7.  Test stock concurrency and transaction behavior where feasible.
8.  Test soft deletion and history preservation.
9.  Test search, filters and pagination.
10. Review logs and fix errors.
11. Check for leaked secrets.
12. Re-run tests after fixes.

**Deliverable:** Verified application.

**Acceptance criteria:** Mandatory functional and authorization tests
pass; unresolved issues are documented honestly.

### Phase 11 -- Documentation and GitHub submission

Tasks:

1.  Write project overview in README.
2.  Document technology stack.
3.  Document prerequisites.
4.  Document installation steps.
5.  Document `.env` setup without including real secrets.
6.  Document database creation and migrations.
7.  Document seed/demo accounts safely.
8.  Document how to run the application.
9.  Add screenshots.
10. Add `.env.example`.
11. Verify `.env`, credentials and private files are ignored by Git.
12. Review Git status and commit history.
13. Push to a public GitHub repository.
14. Open the public repository URL and verify access.

**Deliverable:** Submission-ready public GitHub repository.

**Acceptance criteria:** Another developer can follow the README to
install and run the application locally.

## 16. Final assessment checklist

### Mandatory requirements

-   [ ] Laravel application created and working.
-   [ ] Laravel Breeze authentication implemented.
-   [ ] MySQL configured.
-   [ ] Admin and Staff roles implemented.
-   [ ] Role middleware implemented.
-   [ ] Role-specific login redirects implemented.
-   [ ] Product full CRUD implemented.
-   [ ] All forms have server-side validation.
-   [ ] Admin dashboard displays system-wide data.
-   [ ] Staff dashboard displays only permitted user-specific data.
-   [ ] Admin can access all records.
-   [ ] Staff can manage only their own records.
-   [ ] Bootstrap or Tailwind UI implemented.
-   [ ] Navigation between pages works.
-   [ ] Routes are clearly defined in `web.php`.
-   [ ] Route groups and named routes are used.
-   [ ] Appropriate GET, POST, PUT/PATCH and DELETE methods are used.
-   [ ] Controllers handle route logic.
-   [ ] Dashboard theme is integrated using Blade layouts and partials.
-   [ ] Login, dashboard and CRUD pages use the theme.

### Optional bonus requirements

-   [ ] Product search implemented.
-   [ ] Product pagination implemented.
-   [ ] Laravel Policies or Gates implemented.
-   [ ] Profile management implemented.

### Additional quality features

-   [ ] Category management.
-   [ ] Supplier management.
-   [ ] Stock IN.
-   [ ] Stock OUT.
-   [ ] Stock adjustment.
-   [ ] Stock movement history.
-   [ ] Low-stock alerts.
-   [ ] Out-of-stock alerts.
-   [ ] Automated tests.
-   [ ] Responsive UI.
-   [ ] README and `.env.example`.
-   [ ] Public GitHub repository.

## 17. AI coding agent instructions

The coding agent must follow these rules throughout implementation.

### 17.1 Inspect before changing

-   Inspect the complete existing project structure before making
    changes.
-   Read `composer.json`, relevant Laravel configuration, migrations,
    routes, models, controllers, views and tests.
-   Identify the Laravel and PHP versions.
-   Identify the database configuration without printing secrets.
-   Identify existing changes in Git.
-   Do not overwrite or delete existing user work.
-   Do not assume the repository is empty.

### 17.2 Plan before implementation

First produce:

1.  Repository inspection summary.
2.  Requirement-to-existing-code mapping.
3.  Missing features and gaps.
4.  Proposed database schema and migration plan.
5.  Proposed architecture and folder changes.
6.  Proposed UI/theme integration plan.
7.  Risks and compatibility issues.
8.  Ordered implementation tasks.
9.  Tests to be run.

Wait for the user's approval before making code or database changes.

### 17.3 Implement incrementally

-   Implement one phase at a time.
-   Keep changes focused and consistent with Laravel conventions.
-   Preserve existing working functionality.
-   Do not make unrelated refactors.
-   Do not add unnecessary packages.
-   Explain important design decisions briefly.
-   After each phase, list changed files and summarize behavior.
-   Run relevant tests and report exact results.
-   Stop and request approval before high-impact operations.

### 17.4 Database and destructive-operation safety

-   Never run migrations against an unknown or production database.
-   Never run `migrate:fresh`, database resets, destructive seeders or
    data deletion without explicit approval.
-   Confirm the database name and environment before writing to the
    database.
-   Do not expose passwords, API keys or `.env` secrets.
-   Preserve existing data.
-   Do not physically delete product or stock movement history.
-   Do not run commands that overwrite existing files without checking
    them first.

### 17.5 Git and deployment safety

-   Do not commit, push, create a pull request or deploy without
    explicit approval.
-   Do not change branches or discard user changes without approval.
-   Never commit `.env` or credentials.
-   Show the proposed Git commands before executing them if approval is
    required.

### 17.6 Completion reporting

At the end of each phase, report:

-   Phase completed.
-   Features implemented.
-   Files created or changed.
-   Migrations added.
-   Commands run.
-   Tests run and exact results.
-   Known issues.
-   Remaining work.
-   Next proposed phase.

Never claim a feature is complete unless it is implemented and verified.

## 18. Definition of done

The project is ready for submission when:

1.  All mandatory assessment requirements are implemented.
2.  Admin and Staff authorization is enforced on the server.
3.  Product CRUD works with validation.
4.  Dashboard data respects user roles.
5.  Stock operations preserve quantity and movement history.
6.  Category and supplier relationships remain valid.
7.  The application has a consistent responsive Bootstrap theme.
8.  Automated tests cover important functionality and access
    restrictions.
9.  The README provides reproducible setup instructions.
10. The public GitHub repository is accessible and contains no secrets.

## 19. Recommended first instruction to the coding agent

Copy the following instruction when giving this Markdown specification
to your coding agent:

> Read the complete `StockWise-Implementation-Plan.md` before doing
> anything. Inspect my existing project and compare it with every
> requirement in the document. Do not modify files, run database
> migrations, install packages, execute destructive commands, commit,
> push or deploy during the initial inspection. First return a detailed
> repository assessment, requirement gap analysis, compatibility
> findings, proposed database schema, architecture plan, UI/theme plan
> and phased implementation plan. Preserve all existing work. Wait for
> my explicit approval before implementing Phase 1. After approval,
> implement one phase at a time, verify it with tests and report the
> exact changes and results before moving to the next phase.

------------------------------------------------------------------------

**End of implementation plan.**
