# Laravel Task Manager

This repository contains a Laravel-based Task Manager application. Below are concise instructions to deploy and start the project locally using Docker Compose, and to run it for local development.

**Prerequisites**
- Docker (Engine + Compose)
- Git
- Windows PowerShell (or your preferred shell)

**Quick start (Docker)**
1. Clone the repo:

   git clone git@github.com:gbi46/laravel-task-manager.git
   cd laravel-task-manager

2. Copy environment file and adjust if needed:

   cp .env.example .env
   # If you use SQLite locally, ensure DB_DATABASE points to database/database.sqlite

3. Create local sqlite file (optional if using SQLite):

   mkdir database
   type NUL > database\database.sqlite

4. Start the application stack with Docker Compose (build if needed):

   docker compose up -d --build

5. Run one-time setup inside the app container (migrations, seeding, key):

   docker compose exec app php artisan key:generate --ansi
   docker compose exec app php artisan migrate --force

6. Open the app in your browser:

   http://localhost:8080

Notes: the project uses `DB_CONNECTION=sqlite` by default for local development. If you prefer MySQL, update `.env` and ensure the `db` service is running.

**Local development (without containers)**
1. Install PHP, Composer, Node.js + npm on your host.
2. Install PHP deps and JS deps:

   composer install --prefer-dist --no-interaction
   npm install
   npm run dev

3. Prepare `.env` and app key, then run migrations:

   cp .env.example .env
   php artisan key:generate
   php artisan migrate

4. Serve the app locally:

   php artisan serve --host=0.0.0.0 --port=8000

**Useful commands**
- Tail logs: `docker compose logs -f`
- Restart services: `docker compose restart`
- Run artisan commands: `docker compose exec app php artisan <command>`

**Deploy notes**
- This README covers local/dev deployment. For production, use a proper orchestration and secrets management, run `composer install --no-dev`, and configure a production `docker-compose` or use your cloud provider.

If you want, I can add environment-specific Compose files or CI/CD config for deployment.
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
