# StockWise — Implementation Progress

Source of requirements: `StockWise-Implementation-Plan.md` (unchanged).
This file records the actual state of the code. Status labels: `NOT STARTED`, `IN PROGRESS`, `COMPLETED`, `BLOCKED`.

## Phase status overview

| Phase | Title | Status |
|---|---|---|
| 1 | Repository inspection and project setup | COMPLETED (MySQL connection verified) |
| 2 | Database and Eloquent relationships | COMPLETED (migrated + seeded on MySQL `stockwise`) |
| 3 | Authentication, roles and authorization | COMPLETED |
| 4 | Product CRUD | COMPLETED |
| 5 | Category and supplier management | COMPLETED |
| 6 | Stock management | COMPLETED |
| 7 | Dashboards and reports (+ Module 7 user management) | COMPLETED |
| 8 | UI/UX and dashboard theme | COMPLETED |
| 9 | Profile management and optional bonuses | COMPLETED |
| 10 | Full testing, security and regression | COMPLETED (automated on SQLite + smoke-tested on MySQL) |
| 11 | Documentation and GitHub submission | IN PROGRESS (docs done; commit/push BLOCKED on approval) |

---

## Done vs. still to do (as of 2026-11-14)

### ✅ Already done (implemented and verified)

| # | Item | Verified by |
|---|---|---|
| 1 | Laravel 13.42 project + Laravel Breeze (Blade) installed; portable PHP 8.4 at `C:\tools\php84` | `artisan --version`, Breeze tests |
| 2 | `.env` / `.env.example` configured for MySQL database `stockwise` (password left blank) | file review |
| 3 | Database schema: users (role/status), categories, suppliers, products (soft deletes, decimal prices), stock_movements; restrictive foreign keys + indexes | migrate on SQLite, FK delete tests |
| 4 | Eloquent models, relationships, factories, idempotent demo seeder (1 admin, 2 staff, sample data) | seeder run twice, quantity = movement ledger |
| 5 | Admin / Staff roles, `role` middleware, inactive-user block, role-aware login redirect | `RoleAccessTest` |
| 6 | Public registration and self-account-deletion removed (security) | tests |
| 7 | `ProductPolicy` ownership rules (staff = own products only) | `ProductPolicyTest`, crafted-request tests |
| 8 | Product CRUD: create/list/view/edit/archive, image upload, search, filters, sorting, pagination | `ProductCrudTest` |
| 9 | Admin archived-products view + restore | test |
| 10 | Category and supplier CRUD (admin only), delete blocked while in use | `CategorySupplierTest` |
| 11 | Stock IN / OUT / Adjustment via `StockService` (transaction + row lock, never negative, audit history, no edit/delete of movements) | `StockMovementTest` incl. rollback test |
| 12 | User management (create, edit, role, activate/deactivate, last-admin protection) | `UserManagementTest` |
| 13 | Admin and Staff dashboards (database-driven, scoped) | `DashboardReportTest` |
| 14 | Admin reports (in/out summary, low-stock, out-of-stock, top products) | `DashboardReportTest` |
| 15 | Profile management (name, unique email, password with current password; no role change) | `ProfileTest`, `PasswordUpdateTest` |
| 16 | AdminLTE 4 + Bootstrap 5 theme, Navy/Emerald palette, layouts/partials/components, flash messages, confirm dialogs, empty states, themed error pages, responsive | Chrome browser checks (desktop + 390 px) |
| 17 | Quality & security checks | `php artisan test` → **114 passed / 457 assertions**; `pint --test` passed; `composer audit` + `npm audit` clean; secrets git-ignored |
| 18 | README (setup, demo accounts, tests, screenshots) and `docs/screenshots/` | file review |
| 19 | MySQL password set in `.env` (git-ignored; not committed) | `git check-ignore .env` |
| 20 | MySQL database `stockwise` created (utf8mb4) and migrated + seeded | `migrate --seed` → 8 migrations DONE; 3 users, 4 categories, 3 suppliers, 7 products, 10 movements |
| 21 | MySQL schema checked | 5 foreign keys all `RESTRICT`; `decimal(12,2)` prices; `int unsigned` quantity; `deleted_at` present; every product quantity = movement ledger |
| 22 | App smoke-tested on MySQL (`php artisan serve`, port 8000) | Admin login → all admin pages 200; staff login → staff pages 200, `/admin/*` 403, other staff's product 403, sees 3 own products |
| 23 | `StockService` on MySQL/InnoDB (`lockForUpdate`) | OUT 5 → 35→30; OUT 10000 rejected; other staff rejected (403); all inside a rolled-back transaction — demo data unchanged |

### ⏳ Still to do (needs the user)

| # | Item | Who | How |
|---|---|---|---|
| 1 | Decide on skeleton files `CLAUDE.md` / `AGENTS.md` (they tell AI agents to install Laravel Boost) | **User** | Keep or delete before committing |
| 2 | Approve Git commit | **User** | Agent runs `git add -A` + `git commit` after approval |
| 3 | Create a public GitHub repository and approve push | **User** | Provide repo URL; agent runs `git remote add origin …` + `git push -u origin main` |
| 4 | Open the public repo URL and confirm it is accessible and contains no `.env` | Agent / User | Browser check |

### Optional (not required by the plan)
- CSV/PDF export of reports (plan says to avoid until mandatory work is done).
- Real concurrent-request test of stock locking on MySQL (locking is implemented; SQLite cannot test it).

---

## Log

### 2026-11-14 — Phase 1: Repository inspection

**Findings**
- Repository: only `StockWise-Implementation-Plan.md`; Git branch `main`, no commits, clean tree.
- PHP 8.2.12 (XAMPP, `C:\xampp\php\php.exe`, not on PATH). Extensions present: pdo_mysql, pdo_sqlite, mbstring, openssl, fileinfo, tokenizer, xml, bcmath, curl. **GD not enabled** (affects only fake image generation in tests).
- Composer: **not installed** → using `composer.phar` downloaded into the session scratchpad (tool only, not a project dependency).
- Node v24.14.0, npm 11.9.0.
- MySQL Community Server 8.0.45 running as service `MySQL80` on port 3306. `root` requires a password (not known to the agent).

**Decisions**
- Laravel 12 (supports PHP 8.2) + Laravel Breeze (Blade stack).
- UI: Bootstrap 5 + AdminLTE 4 replacing Breeze's Tailwind views (required by plan §4/§5).
- Tests: PHPUnit with in-memory SQLite (Laravel default) so tests never touch the MySQL dev database.

### 2026-11-14 — Phase 1: Project setup

**User decisions**
- Laravel **13** requested by user. Laravel 13 requires PHP ^8.3, so (with user approval) a portable **PHP 8.4.27** (official windows.php.net NTS x64 zip, SHA-256 verified) was installed to `C:\tools\php84`. XAMPP and system PATH unchanged. Extensions enabled in its `php.ini`: curl, fileinfo, gd, intl, mbstring, openssl, pdo_mysql, pdo_sqlite, sqlite3, zip, sodium.
- MySQL credentials: user will fill in `DB_PASSWORD` in `.env` and create the `stockwise` database.

**Done**
- Laravel Framework **v13.42.0** skeleton created (in scratchpad, then copied into project root; no existing files overwritten).
- `laravel/breeze` v2.4 (dev) installed, Blade stack; Breeze ran `npm install` + `vite build` successfully.
- `.env` / `.env.example`: `APP_NAME=StockWise`, `DB_CONNECTION=mysql`, `DB_DATABASE=stockwise`, `DB_USERNAME=root`, `DB_PASSWORD=` (blank — to be filled by user).
- Not installed: Laravel Boost (suggested by skeleton `CLAUDE.md`/`AGENTS.md`, not requested).

**Tests**
- `php artisan test` → **25 passed, 61 assertions** (Breeze baseline, SQLite in-memory).

**Pending / blocked**
- `BLOCKED` (waiting on user): verify app against MySQL `stockwise` DB — requires `DB_PASSWORD` in `.env` and the database to exist.

Note: run commands with `C:\tools\php84\php.exe` (e.g. `C:\tools\php84\php.exe artisan test`).

### 2026-11-14 — Phase 2: Database and Eloquent relationships — COMPLETED (verified on SQLite; MySQL run pending creds)

**Migrations created**
- `2026_11_14_000001_add_role_and_status_to_users_table` — `role` VARCHAR(20) default `staff`, `status` VARCHAR(20) default `active` (both indexed). Extends Breeze users table (no duplicate table).
- `2026_11_14_000002_create_categories_table` — `name` unique, `description` nullable.
- `2026_11_14_000003_create_suppliers_table` — `name` (indexed), `email`/`phone(50)`/`address` nullable.
- `2026_11_14_000004_create_products_table` — `sku(100)` unique, DECIMAL(12,2) prices, UNSIGNED INT `quantity` (indexed) / `min_stock_level` default 5, `image`, SoftDeletes; FKs `category_id`, `supplier_id` (nullable), `created_by`→users, all `RESTRICT` on delete.
- `2026_11_14_000005_create_stock_movements_table` — `product_id`, `user_id` FKs `RESTRICT`; `type` (indexed), unsigned `quantity`/`previous_quantity`/`new_quantity`, `reason` nullable, indexed `created_at`.

**Models** (`app/Models`)
- `User` (modified): role/status constants, `isAdmin()/isStaff()/isActive()/area()`, `createdProducts()`, `stockMovements()`, `scopeActiveAdmins`. `role`/`status` **not fillable** (no mass-assignment escalation).
- `Category`, `Supplier`: `products()`, `isInUse()` (counts archived products too).
- `Product`: SoftDeletes, `category()/supplier()/creator()/stockMovements()`, `stockStatus()` (single definition per plan §13), `scopeVisibleTo(User)`, `scopeWithStockStatus()`. `created_by` and `quantity` **not fillable**.
- `StockMovement`: type constants, `product()` (withTrashed), `user()`, `signedChange()`, `scopeVisibleTo(User)`; no fillable attributes.

**Factories / seeders**
- `UserFactory` (modified: role/status + `admin()/staff()/inactive()` states), `CategoryFactory`, `SupplierFactory`, `ProductFactory` (`ownedBy()`, `quantity()`), `StockMovementFactory`.
- `DatabaseSeeder`: 1 admin + 2 staff, 4 categories, 3 suppliers, 7 products across owners, initial-stock IN movements. Idempotent; refuses to run when `APP_ENV=production`.

**Verification** (throwaway SQLite file in scratchpad, via env override — MySQL untouched)
- `migrate --seed` → all 8 migrations DONE; second `db:seed` → no duplicates (users=3, categories=4, suppliers=3, products=7, movements=6).
- Relationships resolved (category/supplier/creator/movement user). `visibleTo(staff1)=3`, `visibleTo(admin)=7`.
- Stock status: PHP `stockStatus()` and SQL scope agree (in/low/out).
- Deleting an in-use category → blocked by FK. Soft-deleting a product hides it, keeps movements; force-deleting a product with history → blocked by FK.

### 2026-11-14 — Phase 3: Authentication, roles and authorization — COMPLETED

**Implemented**
- `RoleMiddleware` (alias `role`, e.g. `role:admin`) → 403 for wrong role. Registered in `bootstrap/app.php` (Laravel 11+ mechanism).
- `EnsureUserIsActive` appended to the `web` group → deactivated users are logged out on their next request.
- `LoginRequest`: correct credentials + `status=inactive` → login refused with a clear message.
- Role-aware redirect: `GET /dashboard` (`DashboardController`, invokable) → `admin.dashboard` (`/admin/dashboard`) or `staff.dashboard` (`/staff/dashboard`). `/` → `/dashboard`.
- `Admin\DashboardController` and `Staff\DashboardController` (database-driven stats; staff scoped to own products — full verification in Phase 7).
- `ProductPolicy` (auto-discovered): view/update/delete/manageStock = admin or owner; restore = admin; forceDelete = never.
- `app/helpers.php` → `area_route('products.index')` resolves to the current user's `admin.*`/`staff.*` route (autoloaded via `composer.json` "files").
- **Public registration disabled** (routes, controller, view, test removed) — accounts are admin-created, so no visitor can self-register or become Admin.
- **Breeze self-service account deletion removed** (route, controller method, view partial) — protects audit history and the last-admin rule.
- `/profile` can only change name/email (role/status not fillable; verified by test).

**UI foundation pulled forward from Phase 8** (needed by every view — avoids building Tailwind views and rewriting them)
- npm: removed `tailwindcss`, `@tailwindcss/forms`, `@tailwindcss/vite`, `alpinejs`, `autoprefixer`, `postcss` (+ `tailwind.config.js`, `postcss.config.js`); added `bootstrap@5.3.9`, `@popperjs/core@2.11.8`, `admin-lte@4.10.0`, `bootstrap-icons@1.13.2` (all required by plan §4/§5).
- `resources/css/app.css`: AdminLTE + Bootstrap Icons + palette tokens (plan §5.2). `resources/js/app.js`: Bootstrap + AdminLTE + shared confirm-dialog handler.
- Layouts: `layouts/app` (AdminLTE shell + confirm modal), `layouts/guest`. Partials: `navbar`, `sidebar` (role-specific menus), `footer`, `flash-messages`, `recent-movements`, `attention-products`.
- Components: `nav-item`, `status-badge`, `stock-status`, `movement-type`, `role-badge`, `stat-card`, `empty-state`, `confirm-form`, `form.input`, `form.select`, `form.textarea`.
- Restyled Breeze views: `auth/login|forgot-password|reset-password|confirm-password|verify-email`, `profile/edit` + partials. Removed Tailwind-only Breeze components/views.
- Views: `admin/dashboard`, `staff/dashboard`.

**Tests** (`php artisan test`)
- New: `tests/Feature/RoleAccessTest.php` (11 tests), `tests/Unit/ProductPolicyTest.php` (4 tests); `ProfileTest` updated (deletion unavailable, unique email, no role escalation); `ExampleTest` updated (`/` redirect); `RegistrationTest` removed.
- Result: **39 passed, 112 assertions**. `npm run build` OK.

### 2026-11-14 — Phase 4: Product CRUD — COMPLETED

**Design decisions**
- One `ProductController` registered as `Route::resource('products')` in both groups → `/admin/products/*` (`admin.products.*`) and `/staff/products/*` (`staff.products.*`). Staff listing scoped by `Product::visibleTo()`; every record action authorized via `Gate::authorize()` + `ProductPolicy`; Form Requests also authorize.
- **Initial quantity** is recorded through `StockService` as a `Stock IN` movement ("Initial stock"), so quantity and audit history agree from day one. `StockService` was therefore created now (it is the Phase 6 core); its UI comes in Phase 6.
- **Edit never changes quantity** (`quantity` not in `UpdateProductRequest`, not fillable) — verified by test.
- **Archive rule:** soft-delete only when quantity is 0 (otherwise an error flash explains to record OUT/adjustment first). Movements always preserved; archived products return 404 on normal routes and are hidden from listings.
- SKU normalised to upper case; unique across all products **including archived** (matches the DB unique index).
- Selling ≥ purchase price is **not** enforced (plan marks it optional: "if the business rule requires").

**Files created**
- `app/Services/StockService.php` (transaction + `lockForUpdate`, re-authorizes `manageStock` on the locked row, rejects negative/zero-change/oversize results, writes movement), `app/Exceptions/StockException.php`.
- `app/Http/Controllers/ProductController.php`, `app/Http/Requests/StoreProductRequest.php`, `app/Http/Requests/UpdateProductRequest.php`.
- Views: `products/index` (search name/SKU, filters category/supplier/status/creator(admin), sortable columns, pagination with `withQueryString()`, empty state), `products/create`, `products/edit`, `products/_form`, `products/show` (details, creator for admin, latest 10 movements, archive with confirm dialog). Component `sort-link`.

**Files modified**
- `routes/web.php` (product resources), `app/Models/Product.php` (`imageUrl()`), `app/Providers/AppServiceProvider.php` (`Paginator::useBootstrapFive()`).
- `php artisan storage:link` run (`public/storage` → `storage/app/public`; already git-ignored).

**Tests**
- New `tests/Feature/ProductCrudTest.php` (21 tests): create + audited initial stock, zero-qty no movement, `created_by` ignored from input, required fields, duplicate SKU (incl. archived), invalid category/supplier, invalid numbers, optional supplier, image upload/replace cleanup, non-image rejected, admin-all vs staff-own listing, staff 403 on foreign view/edit/update/delete, staff 403 on admin routes, admin sees creator, search/status filter/pagination preserving query, invalid sort rejected, update keeps quantity, invalid update, admin updates any, archive blocked with stock, archive + history preserved + hidden + 404.
- Result: **60 passed, 203 assertions**.

### 2026-11-14 — Phase 5: Category and supplier management — COMPLETED

**Implemented**
- `CategoryController`, `SupplierController` — full resource CRUD under `role:admin` (`/admin/categories`, `/admin/suppliers`; names `admin.categories.*`, `admin.suppliers.*`).
- `CategoryRequest` (name required, max 255, unique ignoring current, whitespace-normalised; description ≤ 2000) and `SupplierRequest` (name required; email valid; phone regex digits/space/+()-. 6–50; address ≤ 1000; blanks → null). Both also `authorize()` admin-only (defence in depth). *Deviation:* one request per module for store+update instead of `Store*`/`Update*` pairs — same rules, less duplication.
- Delete is blocked (error flash) while any product — **including archived** — references the record; FK `RESTRICT` + `QueryException` catch as the final safeguard.
- Search (categories: name; suppliers: name/email/phone) + pagination with query string; product counts; detail pages listing linked products.
- Views: `categories/{index,create,edit,show,_form}`, `suppliers/{index,create,edit,show,_form}`, partial `product-mini-table`, component `search-form`.
- Staff still get category/supplier dropdowns on the product form (read-only usage).
- Fix: `confirm-form` messages are now passed as plain strings (avoids double-escaped `&quot;` in the dialog); icon-only buttons get a visually-hidden label.

**Tests**
- New `tests/Feature/CategorySupplierTest.php` (11 tests): category CRUD + normalisation, required/unique name, keep own name on update, in-use (archived product) delete blocked, search + pagination; supplier CRUD, contact validation, in-use delete blocked, search by name/email/phone; staff 403 on all category/supplier routes; staff can still select them on product form.
- Result: **71 passed, 257 assertions**.

### 2026-11-14 — Phase 6: Stock management — COMPLETED

**Implemented**
- `StockService` (created in Phase 4) is the single place quantities change: `DB::transaction` → `lockForUpdate()` on the product row → re-authorize `manageStock` on the locked row → compute new quantity (IN: +q, OUT: −q, ADJUSTMENT: set counted level, store |difference|) → reject negative / zero-change / oversize → update product → insert movement (product, user, type, quantity, previous, new, reason) → commit. Any exception rolls back both.
- `StockMovementController` (`index`, `create`, `store` only — **no edit/update/delete**: movements are audit records). Routes: `/admin/stock-movements` (`admin.stock-movements.*`) and `/staff/stock-movements` (`staff.stock-movements.*`).
- `StoreStockMovementRequest`: product required, must exist and not be archived; type ∈ IN/OUT/ADJUSTMENT; quantity integer (≥1 for IN/OUT, ≥0 counted level for ADJUSTMENT); reason required for ADJUSTMENT. User/previous/new quantities are never read from the client. Controller `Gate::authorize('manageStock')` → crafted requests for another user's product get 403.
- History page: scoped (`StockMovement::visibleTo`), filters by product name/SKU, product, type, recorded-by (admin), date range; paginated; archived products shown with an "Archived" badge.
- Product page: "Record stock movement" panel (own/any product per policy) + "Full history" link. Dashboard partials link to products/history.
- Views: `stock/index`, `stock/create`, `stock/_record-form`, `stock/_movement-fields`.
- `DatabaseSeeder` now creates initial stock and sample IN/OUT/ADJUSTMENT movements **through `StockService`** (no duplicated stock logic). Re-verified on throwaway SQLite: 10 movements, every product's quantity equals its movement ledger; re-seeding idempotent.

**Tests**
- New `tests/Feature/StockMovementTest.php` (24 tests): IN increases; OUT decreases; OUT > available rejected with no change; OUT to exactly 0; positive-integer validation; invalid type; adjustment requires reason; adjustment stores absolute difference + previous/new; adjustment to 0 allowed / negative rejected; zero-change adjustment rejected; client-supplied user/previous/new ignored; staff 403 on another user's product (no change); admin records for any product; archived product rejected; staff history scoped; staff create form lists own products only; type/product filters; no edit/update/delete routes; history survives archival; **forced failure on movement insert rolls back the quantity update**; service rejects unauthorized user even if controller bypassed; service rejects blank adjustment reason; service never allows negative stock; quantity always equals movement ledger.
- Result: **95 passed, 341 assertions**.
- Note: true concurrent-request locking cannot be exercised with in-memory SQLite (no row locks); `lockForUpdate()` takes effect on MySQL/InnoDB. Atomicity/rollback is verified.

### 2026-11-14 — Phase 7: Dashboards and reports — COMPLETED

**Implemented**
- Admin dashboard: total products, categories, suppliers, users, total stock quantity, low-stock count, out-of-stock count, recent movements, "needs attention" list, stock-by-category bars. Every stat card links to the matching filtered list.
- Staff dashboard: my products, my stock quantity, my low/out-of-stock, my recent movements, my attention list — all via `Product::visibleTo()` / `StockMovement::visibleTo()`.
- `Admin\ReportController` + `reports/index` (`/admin/reports`): date-range (default last 30 days, validated `to ≥ from`) summary of units IN / OUT, adjustments split up/down, net change; current inventory (products, units, value at purchase price); low-stock report; out-of-stock report; most active products in period. Links drill into the filtered movement history.
- Stock status uses the single definition (`Product::stockStatus()` / `scopeWithStockStatus`) in tables, dashboards and reports.
- Search/filters/sorting/pagination: products (Phase 4), categories, suppliers, movements, users.

### 2026-11-14 — Module 7: User management — COMPLETED

- `UserManagementController` (`/admin/users`, `admin.users.*`): list with search (name/email) + role/status filters + pagination; view (product & movement counts, no password data); create (name, email, role, status, password + confirmation); edit (optional password change); `PATCH /admin/users/{user}/status` activate/deactivate toggle with confirm dialog. **No hard delete** (users own audit history).
- `UserRequest`: admin-only `authorize()`; email lower-cased + unique; role ∈ admin/staff; status ∈ active/inactive; `Password::defaults()` + confirmed.
- Safety: an admin cannot demote/deactivate themselves; the last active admin can never be demoted/deactivated (checked inside a transaction with `lockForUpdate` on active admin rows).
- `role`/`status` assigned explicitly (not fillable); staff get 403 on every user-management route.
- Views: `users/{index,show,create,edit,_form,_status-toggle}`, component `user-status`.
- Removed the temporary `Route::has` guard from `nav-item` now that every sidebar route exists (a test asserts every sidebar link renders and returns 200 per role).

**Tests**
- New `tests/Feature/UserManagementTest.php` (10 tests) and `tests/Feature/DashboardReportTest.php` (7 tests: exact admin/staff dashboard figures, staff scoping of recent movements/attention list, empty states, report summary numbers, date-range validation/application, staff 403 on reports, every sidebar link renders + resolves per role).
- Result: **112 passed, 446 assertions**. `php artisan route:list` → 61 routes, all named, grouped (`admin.*`, `staff.*`) with correct verbs.

### 2026-11-14 — Phase 8: UI/UX and dashboard theme — COMPLETED

The theme foundation was built in Phase 3 (AdminLTE 4 + Bootstrap 5.3 via Vite, Blade layouts/partials/components, palette). This phase verified it **in a real browser** (Chrome DevTools, app served with `php artisan serve` against a throwaway seeded SQLite file in the scratchpad — MySQL untouched).

**Checked in the browser**
- Login (themed, inline validation error shown for wrong credentials), admin login → `/admin/dashboard`, staff login → `/staff/dashboard`.
- Admin dashboard: all stat cards, recent movements, needs-attention, stock-by-category bars render with seeded data.
- Products list: sorting, status badges (icon + text), creator column (admin only), actions.
- Product page: Stock IN via panel → success flash, quantity + history updated; Stock OUT 1000 (> 135) → inline "Insufficient stock" error, stock unchanged, selected type preserved; Archive → Bootstrap confirmation modal → server refuses with error flash because stock > 0.
- Staff products list shows only own 3 products; no "Created by" filter.
- Staff opening `/admin/users` directly → 403.
- Mobile (390 px): cards reflow, sidebar opens via toggle as an overlay, tables scroll horizontally.

**Issues found and fixed**
- "View all" link not right-aligned in flex card headers (AdminLTE clearfix `::after` acted as a flex item) → `.card-header.d-flex::after { display:none }`.
- **Page-level horizontal scroll on mobile** — `.visually-hidden` (position:absolute) labels inside action buttons escaped `.table-responsive` clipping → `.table-responsive { position: relative }`. Verified `scrollWidth == clientWidth` (488 = 488).
- SKU `<code>` rendered in Bootstrap pink (reads as an error) and wrapped mid-code → neutral navy chip, `nowrap`.
- Movement-type fieldset had a stray border → `border-0 p-0`.
- Account-menu toggle had no accessible name on mobile → `aria-label`.
- Console warning (focus retained inside modal being aria-hidden) → confirm button is blurred before the modal hides.
- Laravel default (unthemed) error pages → `resources/views/errors/minimal.blade.php` override themes 403/404/419/500 etc. (verified 404 + 403 render with the StockWise theme and a "Back to dashboard" button).

**Accessibility notes**: status never by colour alone (badges always icon + text); labels on all fields; `aria-invalid` + `aria-describedby` for errors; `aria-current` on active nav; visually-hidden labels on icon-only buttons; `aria-sort` on sorted columns; modal has labelled heading.

**Tests**: **112 passed, 446 assertions** after the fixes. `npm run build` OK.

### 2026-11-14 — Phase 9: Profile management and optional bonuses — COMPLETED

- Profile (Breeze, restyled in Phase 3): update name ✔, update email with uniqueness ✔ (test), change password requiring current password ✔ (Breeze `PasswordUpdateTest`), role/status cannot be changed via profile ✔ (test), self-deletion removed ✔ (test).
- Policies/gates: `ProductPolicy` (view/update/delete/manageStock/restore/forceDelete) used via `Gate::authorize`, `@can`, Form Request `authorize()` and inside `StockService`; role middleware for area-level access.
- Search & pagination complete on products, categories, suppliers, movements, users, archived products (all with `withQueryString()`).
- **Optional bonus added:** admin archived-products view `GET /admin/products/archived` (`admin.products.archived`) with search + pagination + link to history, and `PATCH /admin/products/{id}/restore` (`admin.products.restore`, policy `restore` = admin only). "Archived" button on the admin products page. View `products/archived`.
- Not implemented (plan: "do not add complicated export functionality") — no CSV/PDF export.
- Tests: +1 (`ProductCrudTest::test_admin_can_list_and_restore_archived_products_but_staff_cannot`). Result: **113 passed, 453 assertions**.

### 2026-11-14 — Phase 10: Full testing, security and regression — COMPLETED (on SQLite; MySQL run pending creds)

**Commands run and results**
| Check | Command | Result |
|---|---|---|
| Automated tests | `php artisan test` | **114 passed, 457 assertions** |
| Code style | `vendor/bin/pint --test` | 4 style-only issues fixed (`StockException`, `ProductController`, `StockMovementController` empty bodies; `bootstrap/app.php` imports) → **passed** |
| PHP dependencies | `composer audit` | No security advisories |
| JS dependencies | `npm audit` | 0 vulnerabilities |
| Secrets | `git check-ignore` / status scan | `.env`, `database/*.sqlite`, `storage/logs/*`, `public/storage`, `public/build`, `vendor`, `node_modules` ignored; no `.env`/key/sqlite/log files untracked-but-unignored; `.env.example` has empty `APP_KEY`/`DB_PASSWORD` |
| Logs | `storage/logs/laravel.log` | 1 entry, from the agent's own mistyped `breeze:install --phpunit` during setup — no application errors |
| Browser regression | Chrome DevTools (Phase 8) | Admin + staff flows, validation, flash messages, confirm dialog, empty states, mobile layout, 403/404 pages |

**Coverage of plan §14 / Phase 10 tasks**
- Admin workflows, staff workflows, direct URL access (staff → admin URLs 403, staff → foreign product URLs 403, archived → 404), crafted requests (foreign `product_id` on stock POST → 403; `created_by`/`user_id`/`previous_quantity`/`role` injected in payloads ignored; foreign `product_id` filter on history → no data/name leak — **new test added this phase**), validation failures, soft deletion + history preservation, search/filters/pagination, transaction rollback (forced failure).
- Concurrency: `lockForUpdate()` inside the transaction is implemented; true parallel-request testing is not feasible with in-memory SQLite (no row locks) — to verify on MySQL if desired.

**Manual security review**
- XSS: all output via `{{ }}`; the two `{!! !!}` uses wrap user data in `e()` first.
- SQL injection: Eloquent bindings; LIKE wildcards escaped; sort column/direction whitelisted by validation.
- CSRF on every form; method spoofing for PUT/PATCH/DELETE.
- Mass assignment: `role`, `status`, `created_by`, `quantity` not fillable; `StockMovement` has no fillable fields.
- Uploads: `image`, `mimes:jpg,jpeg,png,webp`, max 2 MB, random filenames, public disk; old file removed only after a successful save.
- Login throttling (Breeze, 5 attempts), inactive users refused at login and logged out mid-session, public registration disabled, no self-deletion, last-admin protection.

### 2026-11-14 — Phase 11: Documentation and GitHub submission — IN PROGRESS (awaiting user approval / credentials)

**Done**
- `README.md` rewritten (replaces the Laravel skeleton README): overview, features, business rules, stack, prerequisites, installation, `.env`/database setup (no secrets), migrate/seed/storage:link, run, demo accounts, tests, structure, screenshots, links to plan and this file.
- `.env.example`: `APP_NAME=StockWise`, MySQL `stockwise` settings, empty `APP_KEY` and `DB_PASSWORD`.
- Screenshots captured from the running app (throwaway SQLite demo data): `docs/screenshots/{login,admin-dashboard,products,product-detail,reports}.png` (1.5 MB total).
- Git hygiene verified: 189 untracked files ready; `.env`, `vendor/`, `node_modules/`, `public/build`, `public/storage`, `storage/logs`, `*.sqlite` are ignored.

**Pending (not done — requires the user)**
- ~~`BLOCKED` — MySQL verification~~ → **COMPLETED 2026-11-14** (see "Done vs. still to do", items 19–23).
- `BLOCKED` — **Commit / push / public repository:** not performed (user instruction: no commit/push without approval). Proposed commands once approved:
  ```
  git add -A
  git commit -m "Build StockWise inventory management system (Laravel 13, Breeze, AdminLTE)"
  git remote add origin <public-repo-url>
  git push -u origin main
  ```
- Decision for the user: the Laravel 13 skeleton ships `CLAUDE.md` and `AGENTS.md` that instruct AI agents to install Laravel Boost. They were left untouched (not part of the plan); remove them before committing if they should not be in the submission.

---

## Current status summary

- Phases 1–10: implemented and verified with automated tests (in-memory SQLite) and in-browser checks (throwaway SQLite DB). Latest run: **114 tests passed, 457 assertions**; `pint --test` passed; `composer audit` / `npm audit` clean; `npm run build` OK.
- 2026-11-14: verified on the MySQL development database `stockwise` (migrated, seeded, schema checked, admin/staff smoke test, stock service on InnoDB).
- Nothing committed or pushed.

**Next recommended step:** decide on `CLAUDE.md` / `AGENTS.md`, then approve the commit and the push to a public GitHub repository.

## Final assessment checklist (plan §16) — actual status

| Requirement | Status |
|---|---|
| Laravel application created and working | COMPLETED (Laravel 13.42) |
| Laravel Breeze authentication | COMPLETED |
| MySQL configured | COMPLETED — `stockwise` database migrated, seeded and smoke-tested |
| Admin and Staff roles | COMPLETED |
| Role middleware | COMPLETED (`role:` alias) |
| Role-specific login redirects | COMPLETED |
| Product full CRUD | COMPLETED |
| Server-side validation on all forms | COMPLETED (Form Requests / `validate()`) |
| Admin dashboard system-wide data | COMPLETED |
| Staff dashboard only permitted data | COMPLETED |
| Admin can access all records | COMPLETED |
| Staff can manage only own records | COMPLETED |
| Bootstrap UI | COMPLETED (Bootstrap 5.3 via AdminLTE 4) |
| Navigation works | COMPLETED (test: every sidebar link renders + 200) |
| Routes clearly defined in `web.php`, groups, named routes, correct verbs | COMPLETED |
| Controllers handle route logic | COMPLETED (no closures except `Route::redirect`) |
| Theme integrated via Blade layouts/partials; login/dashboard/CRUD themed | COMPLETED |
| Bonus: search, pagination, policies/gates, profile management | COMPLETED |
| Categories, suppliers, stock IN/OUT/adjustment, history, low/out-of-stock alerts | COMPLETED |
| Automated tests | COMPLETED (114 tests) |
| Responsive UI | COMPLETED (verified at 390 px) |
| README and `.env.example` | COMPLETED |
| Public GitHub repository | BLOCKED — awaiting user approval |
