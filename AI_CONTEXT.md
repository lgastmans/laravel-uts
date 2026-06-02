# Project Overview

This is a Laravel-based vehicle/taxi operations application for managing transport bills, customers, drivers, vehicle owners, vehicles, and model-based vehicle pricing.

The business domain is taxi/vehicle rental operations. The app appears to support importing trip/bill data from an external legacy billing source, reviewing bills in a dashboard table, exporting invoices to Zoho Books, and maintaining master data needed for operations.

Main workflows currently implemented:

- User authentication with Laravel Breeze/Livewire.
- Dashboard bill review through a Livewire PowerGrid table.
- API bill sync from an external source via `POST /api/sync-bills` using `X-Sync-Token`.
- Export selected bills from the dashboard to Zoho Books invoices.
- CRUD management for Customers, Drivers, Owners, Vehicles, and Vehicle Model Prices.
- Vehicle pricing is maintained per vehicle model, not per individual vehicle.

# Technology Stack

- Laravel: `^12.0` from `composer.json`.
- PHP: `^8.2` from `composer.json`.
- Database: Laravel default is SQLite unless overridden by `.env` (`config/database.php` defaults to `DB_CONNECTION=sqlite`). MySQL, MariaDB, PostgreSQL, and SQL Server configs are present as standard Laravel options.
- Frontend: Blade, Livewire 3, Livewire Volt, Alpine-style Blade interactions, Tailwind CSS, Vite.
- Authentication: Laravel Breeze with Livewire/Volt auth pages and session guard.
- Main packages:
  - `livewire/livewire` `^3.4`
  - `livewire/volt` `^1.7.0`
  - `power-components/livewire-powergrid` `^6.3`
  - `laravel/breeze` `^2.3` dev dependency
  - `laravel/sail`, `laravel/pint`, `phpunit`, `laravel/pail` dev tools
  - Frontend dev packages include Tailwind CSS, Vite, Axios, Concurrently, and Flatpickr.

# Architecture

The application follows a mostly standard Laravel MVC structure, with Livewire PowerGrid components used for tabular listings.

High-level architecture:

- Routes are registered in `routes/web.php` for web CRUD resources and in `routes/api.php` for bill sync.
- Controllers live in `app/Http/Controllers` and use inline `$request->validate([...])` validation.
- Eloquent models live in `app/Models`.
- PowerGrid table components live in `app/Livewire`.
- Blade views live in `resources/views`, grouped by resource folder.
- App layout/navigation is in `resources/views/layouts/app.blade.php` and `resources/views/livewire/layout/navigation.blade.php`.
- Zoho Books integration is centralized in `app/Services/ZohoApiService.php` and consumed by `app/Livewire/BillsTable.php`.

Important design decisions:

- CRUD modules are conventional Laravel resource controllers using route model binding where implemented cleanly.
- Listing pages generally use Livewire PowerGrid components.
- Most index pages include an inline modal create form triggered by the table header button.
- Standalone create and edit views also exist for resource routes.
- Vehicles use `vehicle_model_price_id` instead of a plain text `model` field, so price is linked to a vehicle model price record.
- Vehicle owner is optional: `vehicles.owner_id` is nullable and uses `nullOnDelete()`.
- Vehicle model price is required: `vehicles.vehicle_model_price_id` is non-null and uses `restrictOnDelete()`.

Naming conventions:

- Controllers: singular resource name + `Controller`, e.g. `VehicleController`.
- Models: singular PascalCase, e.g. `VehicleModelPrice`.
- Tables: plural snake_case, e.g. `vehicle_model_prices`.
- Resource view folders: plural kebab-case or plural lower-case, e.g. `customers`, `vehicle-model-prices`.
- Route names: Laravel resource route names, e.g. `vehicles.index`, `vehicle-model-prices.edit`.
- Livewire tables: singular PascalCase + `Table`, e.g. `VehicleTable`.

# Database Structure

## users

Purpose: Authenticated application users.

Key fields:

- `id`
- `name`
- `email` unique
- `email_verified_at` nullable
- `password`
- `remember_token`
- timestamps

Relationships:

- No explicit app-specific relationships currently defined.

## password_reset_tokens

Purpose: Laravel password reset token storage.

Key fields:

- `email` primary key
- `token`
- `created_at`

Relationships:

- No explicit Eloquent model in this app.

## sessions

Purpose: Laravel database-backed session storage.

Key fields:

- `id` primary key
- `user_id` nullable indexed
- `ip_address`
- `user_agent`
- `payload`
- `last_activity`

Relationships:

- No explicit Eloquent model in this app.

## cache

Purpose: Laravel cache storage when using database cache driver.

Key fields:

- `key` primary key
- `value`
- `expiration`

Relationships:

- None.

## cache_locks

Purpose: Laravel atomic lock storage for database cache driver.

Key fields:

- `key` primary key
- `owner`
- `expiration`

Relationships:

- None.

## jobs

Purpose: Laravel queued job storage.

Key fields:

- `id`
- `queue`
- `payload`
- `attempts`
- `reserved_at`
- `available_at`
- `created_at`

Relationships:

- None.

## job_batches

Purpose: Laravel queue batch metadata.

Key fields:

- `id` primary key
- `name`
- `total_jobs`
- `pending_jobs`
- `failed_jobs`
- `failed_job_ids`
- `options`
- `cancelled_at`
- `created_at`
- `finished_at`

Relationships:

- None.

## failed_jobs

Purpose: Failed queued job storage.

Key fields:

- `id`
- `uuid` unique
- `connection`
- `queue`
- `payload`
- `exception`
- `failed_at`

Relationships:

- None.

## bills

Purpose: Stores imported transport bills/trips from an external source. These records are displayed on the dashboard and can be exported to Zoho Books.

Key fields:

- `id` local primary key
- `bill_id` unique external source ID
- `bill_number`
- `bill_date`
- `customer` text name from source
- `from_place`, `to_place`
- `dep_date`, `dep_time`
- `arr_date`, `arr_time`
- `vehicle_reg_no`
- `amount`
- `car`
- `driver_id` external/source driver id, not currently related to `drivers.id`
- `zoho_invoice_id`
- `zoho_customer_id`
- `synced_at`
- timestamps

Relationships:

- `Customer::bills()` maps `customers.zoho_contact_id` to `bills.zoho_customer_id` using custom keys.
- `Bill` does not currently define relationships to Customer, Driver, Vehicle, or Owner.

Implementation note:

- `BillSyncController` tries to accept fields such as `Biller`, `BillType`, and `Community`, but the current `bills` migration does not define `biller`, `bill_type`, or `community` columns and `Bill::$fillable` does not include them.

## customers

Purpose: Customer master data for billing/Zoho contact mapping.

Key fields:

- `id`
- `name`
- `email` nullable
- `phone` nullable
- `gst_number` nullable
- `address` nullable
- `zoho_contact_id` nullable
- timestamps

Relationships:

- `Customer hasMany Bill` via `hasMany(Bill::class, 'zoho_customer_id', 'zoho_contact_id')`.

## drivers

Purpose: Driver master data.

Key fields:

- `id`
- `name`
- `email` nullable
- `phone` nullable
- `gst_number` nullable
- `address` nullable
- `zoho_contact_id` nullable
- timestamps

Relationships:

- No explicit relationships currently defined.
- `bills.driver_id` is an external/source integer and is not currently a foreign key to `drivers.id`.

## owners

Purpose: Vehicle owner master data.

Key fields:

- `id`
- `name`
- `phone` nullable
- `email` nullable
- `address` nullable
- timestamps

Relationships:

- `Owner hasMany Vehicle`.

## vehicle_model_prices

Purpose: Price list per vehicle model. This is model-level pricing, not per-vehicle pricing.

Key fields:

- `id`
- `model` unique
- `day_rent` decimal(10,2), default 0
- `rate_per_km` decimal(10,2), default 0
- `hourly_charge` decimal(10,2), default 0
- `local_charge` decimal(10,2), default 0
- timestamps

Relationships:

- `VehicleModelPrice hasMany Vehicle`.

## vehicles

Purpose: Vehicle fleet master data.

Key fields:

- `id`
- `owner_id` nullable foreign key to `owners.id`
- `vehicle_model_price_id` required foreign key to `vehicle_model_prices.id`
- `registration_number` unique
- `insurance_validity` nullable date
- `permit_validity` nullable date
- timestamps

Relationships:

- `Vehicle belongsTo Owner`.
- `Vehicle belongsTo VehicleModelPrice`.

# Models and Relationships

## Bill

File: `app/Models/Bill.php`

- Fillable source/export fields include bill IDs, dates, customer name, locations, vehicle registration, amount, driver id, Zoho invoice id, and `synced_at`.
- No relationships are defined on the Bill model.

## Customer

File: `app/Models/Customer.php`

- `hasMany(Bill::class, 'zoho_customer_id', 'zoho_contact_id')`.
- This relationship uses Zoho contact IDs rather than a normal local customer foreign key.

## Driver

File: `app/Models/Driver.php`

- No relationships currently defined.

## Owner

File: `app/Models/Owner.php`

- `hasMany(Vehicle::class)`.

## Vehicle

File: `app/Models/Vehicle.php`

- `belongsTo(Owner::class)`.
- `belongsTo(VehicleModelPrice::class)` through `vehicleModelPrice()`.
- Casts `insurance_validity` and `permit_validity` as dates.

## VehicleModelPrice

File: `app/Models/VehicleModelPrice.php`

- `hasMany(Vehicle::class)`.

## User

File: `app/Models/User.php`

- Standard Laravel authenticatable user model.

No many-to-many relationships are currently implemented.

# CRUD Modules

## Customers

Routes:

- `Route::resource('customers', CustomerController::class)`
- Route names: `customers.index`, `customers.create`, `customers.store`, `customers.edit`, `customers.update`, `customers.destroy`, `customers.show`

Controller:

- `app/Http/Controllers/CustomerController.php`

Validation:

- `name`: required string max 255
- `email`: nullable email max 255
- `phone`: nullable string max 20
- `gst_number`: nullable string max 255
- `address`: nullable string max 500

Views:

- `resources/views/customers/index.blade.php`
- `resources/views/customers/create.blade.php`
- `resources/views/customers/edit.blade.php`
- `resources/views/customers/show.blade.php` exists but is not implemented meaningfully

Listing:

- `app/Livewire/CustomerTable.php`
- Shows columns for name, email, phone, GST number, address, Zoho contact id, created timestamps, and edit action.

Known issue:

- `CustomerController::destroy(string $id)` references `$customer` without defining it. Delete route exists but will fail unless fixed to use route model binding or lookup by id.

## Drivers

Routes:

- `Route::resource('drivers', DriverController::class)`

Controller:

- `app/Http/Controllers/DriverController.php`

Validation:

- Same shape as Customers: `name`, `email`, `phone`, `gst_number`, `address`.

Views:

- `resources/views/drivers/index.blade.php`
- `resources/views/drivers/create.blade.php`
- `resources/views/drivers/edit.blade.php`

Listing:

- `app/Livewire/DriverTable.php`
- Shows driver fields and edit action.

## Owners

Routes:

- `Route::resource('owners', OwnerController::class)`

Controller:

- `app/Http/Controllers/OwnerController.php`

Validation:

- `name`: required string max 255
- `phone`: nullable string max 20
- `email`: nullable email max 255
- `address`: nullable string max 500

Views:

- `resources/views/owners/index.blade.php`
- `resources/views/owners/create.blade.php`
- `resources/views/owners/edit.blade.php`

Listing:

- `app/Livewire/OwnerTable.php`
- Shows name, phone, email, address, created timestamps, edit action, and delete action.

## Vehicles

Routes:

- `Route::resource('vehicles', VehicleController::class)`

Controller:

- `app/Http/Controllers/VehicleController.php`

Validation:

- `owner_id`: nullable, must exist in `owners.id`
- `vehicle_model_price_id`: required, must exist in `vehicle_model_prices.id`
- `registration_number`: required string max 255 unique in `vehicles.registration_number`; update uses `Rule::unique(...)->ignore($vehicle->id)`
- `insurance_validity`: nullable date
- `permit_validity`: nullable date

Views:

- `resources/views/vehicles/index.blade.php`
- `resources/views/vehicles/create.blade.php`
- `resources/views/vehicles/edit.blade.php`

Listing:

- `app/Livewire/VehicleTable.php`
- Shows registration number, model name via `vehicleModelPrice`, owner name, insurance validity, permit validity, and actions.

## Vehicle Model Prices

Routes:

- `Route::resource('vehicle-model-prices', VehicleModelPriceController::class)`

Controller:

- `app/Http/Controllers/VehicleModelPriceController.php`

Validation:

- `model`: required string max 255 unique in `vehicle_model_prices.model`; update ignores current id
- `day_rent`: required numeric min 0
- `rate_per_km`: required numeric min 0
- `hourly_charge`: required numeric min 0
- `local_charge`: required numeric min 0

Views:

- `resources/views/vehicle-model-prices/index.blade.php`
- `resources/views/vehicle-model-prices/create.blade.php`
- `resources/views/vehicle-model-prices/edit.blade.php`

Listing:

- `app/Livewire/VehicleModelPriceTable.php`
- Shows model and all price fields with edit and delete actions.
- Livewire delete handler prevents deleting a price record if vehicles are assigned to it.

## Bills Dashboard

Routes:

- `GET /dashboard` returns `resources/views/dashboard.blade.php` with auth and verified middleware.

Components:

- `app/Livewire/BillsTable.php`

Behavior:

- Lists bills with PowerGrid.
- Provides filters for bill number, bill date, customer, departure date, arrival date, and vehicle registration number.
- Allows selecting rows and dispatching `exportSelectedBills`.
- Exports selected bills to Zoho Books using `ZohoApiService`.
- Dashboard includes a JavaScript-driven bulk message alert for export status.

## API Bill Sync

Routes:

- `POST /api/sync-bills` handled by `App\Http\Controllers\Api\BillSyncController@sync`.

Behavior:

- Requires `X-Sync-Token` header to match `config('app.sync_secret')`.
- Expects request body to be an array of bill objects.
- Skips rows without `BillId`.
- Upserts by external `BillId` into `bills.bill_id`.
- Applies `config('app.invoice_prefix')` to `bill_number`.

# Business Rules

Current implemented rules:

- Owner name is required.
- Customer and Driver names are required.
- Vehicle registration number is required and unique.
- Vehicle must select a vehicle model price record.
- Vehicle owner is optional.
- Deleting an owner sets related vehicle `owner_id` to null via database `nullOnDelete()`.
- Deleting a vehicle model price is restricted at database level if vehicles reference it.
- `VehicleModelPriceTable` also checks for assigned vehicles before deleting and shows an error message.
- Vehicle pricing is maintained per model in `vehicle_model_prices`, not per individual vehicle.
- Vehicle insurance and permit validity fields are nullable dates.
- Price fields must be numeric and at least 0.
- External bills are upserted by `BillId` to avoid duplicate source bill rows.
- Bill sync requires a shared secret header.
- Zoho export attempts to avoid duplicate invoices by checking reference number in Zoho.
- Zoho access tokens are cached by client/scope/organization for 3600 seconds.

Known partial or fragile rules:

- `bills.driver_id` is not connected to the local `drivers` table.
- `bills.vehicle_reg_no` is not connected to the local `vehicles.registration_number`.
- `bills.customer` is plain text; customer relation uses Zoho ID fields only and may not be populated by sync.
- Some Zoho service methods contain stale variable names/comments such as vendor/customer wording.

# User Interface

Navigation:

Desktop navigation currently includes:

- Dashboard
- Customers
- Drivers
- Owners
- Vehicles
- Model Prices

Responsive/mobile navigation currently only shows Dashboard plus profile/logout controls; the CRUD links have not been mirrored into the mobile menu.

Dashboard:

- Main authenticated landing page after login.
- Shows `BillsTable` for imported bills.
- Includes a hidden bulk alert box displayed via the `showBulkMessages` browser event.

Forms:

- Resource index pages generally include modal create forms.
- Standalone create and edit Blade views exist for resource route completeness.
- Forms use Tailwind utility classes and standard Blade components/layout.

Tables:

- PowerGrid provides search input, pagination/per-page controls, record counts, sortable/searchable columns where configured, and checkbox selection.
- Customers and Drivers currently expose edit actions.
- Owners, Vehicles, and Vehicle Model Prices expose edit and delete actions.

# Development Conventions

Coding style:

- Standard Laravel PHP classes with namespaces under `App\...`.
- Inline validation in controller methods rather than Form Request classes.
- Mostly compact controllers with simple `index`, `create`, `store`, `edit`, `update`, `destroy` actions.
- Blade views use Tailwind CSS utility classes.
- Livewire PowerGrid components define `setUp`, `datasource`, `header`, `fields`, `columns`, `filters`, and `actions`.

Validation approach:

- `$request->validate([...])` inside controller methods.
- Unique update rules use `Illuminate\Validation\Rule::unique(...)->ignore($model->id)`.
- Required foreign keys use `exists:table,id`.

Route naming conventions:

- Laravel resource routes with plural URI segments.
- Kebab-case route segment for multi-word resource: `vehicle-model-prices`.

Controller naming conventions:

- Singular PascalCase resource + `Controller`, e.g. `OwnerController`.

View naming conventions:

- Resource views grouped by plural folder name.
- Multi-word resource folder uses kebab-case: `vehicle-model-prices`.
- Common view files are `index.blade.php`, `create.blade.php`, and `edit.blade.php`.

Migration conventions:

- Standard Laravel anonymous migrations.
- Master tables use plural snake_case table names.
- Current new vehicle-related migration timestamps are `2026_06_02_122200`, `2026_06_02_122201`, and `2026_06_02_122202`.

# Future Improvements

Obvious unfinished features and cleanup opportunities:

- Fix `CustomerController::destroy()` to use route model binding: `destroy(Customer $customer)`.
- Add delete buttons to Customers and Drivers tables if full UI delete support is desired.
- Mirror Customers, Drivers, Owners, Vehicles, and Model Prices links into the mobile/responsive navigation menu.
- Add explicit relationships between bills and local vehicles/drivers/customers if source data can be mapped reliably.
- Decide whether `drivers` should include an external/source driver id column to map `bills.driver_id`.
- Decide whether bills should map `vehicle_reg_no` to `vehicles.registration_number`.
- Add missing `biller`, `bill_type`, and `community` columns to `bills` or remove those assignments from `BillSyncController`.
- Add `zoho_customer_id` to `Bill::$fillable` if it is written via mass assignment in future.
- Review `BillSyncController` expression `'bill_number' => $invoicePrefix."-".$data['BillNumber'] ?? null`; due to operator precedence, it assumes `BillNumber` exists when `BillId` exists.
- Harden Zoho integration error handling. Some methods reference stale variables, e.g. `$createResponse` in `getOrCreateVendor()` error return.
- Add feature tests for each CRUD module.
- Add authorization policies if not all authenticated users should manage all records.
- Add database seeders for vehicle model prices and sample owners/vehicles.
- Consider replacing repeated Blade form markup with reusable components or partials.
- Consider Form Request classes if validation grows.
- Run `php artisan migrate` after adding the new migrations if not already applied in the target environment.

# Current Project Status

Completed in code:

- Laravel Breeze/Livewire authentication scaffold.
- Dashboard route and bills listing.
- API bill sync endpoint with token check and bill upsert.
- Zoho Books service and export flow from selected dashboard bills.
- Customers CRUD scaffolding and PowerGrid listing.
- Drivers CRUD scaffolding and PowerGrid listing.
- Owners CRUD scaffolding and PowerGrid listing.
- Vehicles CRUD scaffolding and PowerGrid listing.
- Vehicle Model Prices CRUD scaffolding and PowerGrid listing.
- Migrations for customers, drivers, owners, vehicle model prices, vehicles, bills, users, sessions, cache, and queues.

Needs attention:

- Run `php artisan migrate` in environments where the newest migrations have not been applied.
- Fix known Customer delete bug before using customer deletion.
- Decide and implement mapping between imported bills and local master data.
- Add tests for new CRUD modules and bill sync/export behavior.
- Improve mobile navigation parity.

# Quick Start For Future AI Sessions

Start here:

1. Read `routes/web.php` for all web resources. Current CRUD resources are `customers`, `drivers`, `owners`, `vehicles`, and `vehicle-model-prices`.
2. Read `routes/api.php` for the bill sync endpoint: `POST /api/sync-bills`.
3. For table/listing behavior, inspect the matching Livewire component in `app/Livewire/*Table.php`.
4. For form validation and persistence, inspect the matching controller in `app/Http/Controllers/*Controller.php`.
5. For database shape, inspect migrations in `database/migrations` rather than assuming the database has already been migrated.
6. For Zoho export behavior, inspect `app/Livewire/BillsTable.php` and `app/Services/ZohoApiService.php`.
7. The current UI layout/nav is in `resources/views/livewire/layout/navigation.blade.php`.
8. The dashboard is `resources/views/dashboard.blade.php` and renders `<livewire:bills-table />`.

Important implementation facts:

- Vehicles do not store a plain `model` string; they store `vehicle_model_price_id` and display the model through `VehicleModelPrice`.
- Vehicle owner is optional; vehicle model price is required.
- `vehicle_model_prices.model` and `vehicles.registration_number` are unique.
- `bills` are imported from an external source and currently store source customer/driver/vehicle data mostly as text/IDs, not foreign keys.
- Customers have a Zoho-oriented relationship to bills through `zoho_contact_id` and `zoho_customer_id`.
- Do not assume Customer delete works until `CustomerController::destroy()` is fixed.
- If adding fields to bills, update the migration, `Bill::$fillable`, `BillSyncController`, and `BillsTable` together.
- If adding a new CRUD module, follow the pattern: migration, model, controller, Livewire table, `resources/views/{resource}`, resource route, nav link.

Common commands:

```bash
composer install
npm install
php artisan migrate
npm run dev
php artisan serve
php artisan route:list
php artisan test
```
