# Digit

Digit is a Laravel + Filament site for a sales-and-repairs business. The public site
showcases announcements, services and pricing, and articles, and takes contact form
submissions; a Filament admin panel at `/admin` manages all of that content.

## Stack

- Laravel 13 (PHP 8.3+)
- [Filament 4](https://filamentphp.com) admin panel
- Tailwind CSS 4 + Vite for the public-facing views
- [Laravel Boost](https://laravel.com/docs/ai) for AI-assisted development (see `AGENTS.md` / `CLAUDE.md`)

## Features

- **Public site** (`routes/web.php`, `app/Http/Controllers`): home page with an
  announcements carousel, services & pricing (`/prices`), about page, articles
  (`/articles`), and a rate-limited contact form.
- **Admin panel** (`app/Filament/Resources`): manage `Service`/`ServiceCategory`,
  `Post` (articles), `Announcement`, `ContactMessage`, and `Setting` records.
- **Models** (`app/Models`): `Service`, `ServiceCategory`, `Post`, `Announcement`,
  `ContactMessage`, `Setting`, `User`.

## Run locally

Use Node 22, then run this from the project root:

```bash
nvm use 22
composer run dev
```

This starts the Laravel server and Vite; open [http://localhost:8000](http://localhost:8000).
Running `php -S localhost:8000` alone from the project root does not route requests through
Laravel. If you need PHP's built-in server, use Node 22 to build the assets with `npm run build`,
then run:

```bash
php -S localhost:8000 -t public public/index.php
```

## Testing

```bash
php artisan test
```

Pass a file path or `--filter=testName` to run a narrower set, e.g.
`php artisan test tests/Feature/ContactFormTest.php`.

## Admin panel

The Filament admin panel is available at `/admin` once a user exists. Create one with:

```bash
php artisan make:filament-user
```

## Agentic development

This project uses [Laravel Boost](https://laravel.com/docs/ai) for AI-assisted
development; see `AGENTS.md` and `CLAUDE.md` for the guidelines agents should follow
when working in this codebase.

## License

This project is proprietary; all rights reserved unless stated otherwise.
