# Copilot instructions for Digit

## Project overview

This repository is a Laravel 13 application for a small digital-services and repair business. The app is split between a public storefront and a Filament admin panel:

- Public routes live in `routes/web.php` and render Blade pages for the home page, pricing page, about page, contact page, and article detail pages.
- The main business data is stored in Eloquent models under `app/Models/`: `ServiceCategory`, `Service`, `Post`, `ContactMessage`, and `Setting`.
- The admin panel is configured in `app/Providers/Filament/DgitadminPanelProvider.php` and is available under `/dgitadmin`.
- The admin CRUD resources under `app/Filament/Resources/` manage services, service categories, blog posts, contact messages, and settings.

This project is not a generic Laravel starter. The domain is mainly catalog and content management for a repair/sales business, so changes usually involve Eloquent models, migrations, and Filament resources together.

## Build, test, and lint commands

Use the project scripts from the repository root:

```bash
# install dependencies
composer install
npm install

# local app startup (Vite + Laravel dev server)
composer run dev

# frontend build
npm run build

# full PHP test suite
php artisan test

# run a single test file or filter
php artisan test tests/Feature/ExampleTest.php
php artisan test --filter=ContactController

# PHP formatting/linting
./vendor/bin/pint
./vendor/bin/pint --test
```

For a fresh setup, `composer run setup` also performs the usual project bootstrap: install dependencies, create `.env` if missing, generate an app key, migrate the database, install Node dependencies, and build frontend assets.

## High-level architecture

- `routes/web.php`: the public site entry points and contact form endpoints.
- `app/Http/Controllers/`: request handling for public pages (`PageController`, `PriceController`, `PostController`, `ContactController`).
- `resources/views/`: Blade templates for the storefront; layouts are under `resources/views/layouts/` and page-specific content is in `resources/views/*.blade.php` and `resources/posts/show.blade.php`.
- `app/Models/`: domain models. The main relationships are:
  - `ServiceCategory::services()` -> many `Service` records
  - `Service::category()` -> belongs to a `ServiceCategory`
  - `Post::author()` -> belongs to a `User`
  - `Setting::get()` -> cached setting lookup pattern for site configuration
- `database/migrations/`: schema for services, service categories, posts, contact messages, and settings. If you change schema or model fields, update the matching migration and model together.
- `database/seeders/ServiceSeeder.php`: seeded example catalog data for services and categories; it uses `updateOrCreate` so it is safe to rerun.
- `app/Filament/Resources/`: admin CRUD surfaces. These resources mirror the same domain models and should stay aligned with the Eloquent model definitions.
- `config/` and `bootstrap/`: Laravel runtime wiring; do not change framework defaults unless the task explicitly requires it.

## Key conventions specific to this repo

- The app is organized around a small service catalog rather than a CRUD-heavy generic app. If a feature is about pricing or service offers, prefer the `ServiceCategory` / `Service` flow over ad hoc arrays or inline constants.
- Public pages follow a thin controller + Blade view pattern. Do not move business logic into Blade files for route responses.
- Filament resources and Eloquent models are the source of truth for admin-managed data. When adding a new field to a model, also update the corresponding resource form/table and any validation or relationship expectations.
- Contact form handling preserves a honeypot pattern: `ContactController::send()` rejects requests where `website` is filled, then validates the real fields (`name`, `email`, `phone`, `message`) and stores the record.
- `Setting` reads and writes use the `cache()->remember(...)` pattern and invalidate cache on save; that pattern is the project’s built-in configuration cache approach.
- `Post` uses a `slug` and published-state convention. The public route uses `Route::get('/articles/{post:slug}', ...)`, and `PostController::show()` aborts unless the post is published and its `published_at` date is in the past.
- The project uses Laravel’s default conventions for namespacing and resource discovery. Keep new resources in `app/Filament/Resources/<Domain>/...` and let the panel auto-discover them rather than creating a custom admin routing layer.

## Existing AI guidance files

- `AGENTS.md` and `CLAUDE.md` are the stock Laravel Boost placeholders, not a custom source of truth for this app.
- The real project conventions live in the code structure itself: Laravel + Blade storefront, Filament admin, Eloquent models, and migration-driven schema.

## Working style for this repo

- Prefer the narrowest change that matches the existing Laravel/Filament architecture.
- Keep domain logic near the model or controller that owns it, not scattered across unrelated files.
- If you add or rename a route, make sure the matching controller and Blade view stay in sync.
- When editing seeded catalog content, use the same model structure (`ServiceCategory` + `Service`) instead of hardcoding in a template.
