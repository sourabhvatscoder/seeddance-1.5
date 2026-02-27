# Copilot Instructions for `seeddance-1.5`

## Project snapshot
- Laravel 10 app (`php ^8.1`) with Blade-rendered UI and light vanilla JS.
- Routing is closure-heavy in `routes/web.php` (session-gated pages) and controller-based for API in `routes/api.php`.
- Current auth is intentionally simple: session flag `is_logged_in` with hardcoded credentials in `routes/web.php`.

## Architecture and data flow
- UI pages (`resources/views/home.blade.php`, `dashboard.blade.php`, `prompts.blade.php`) use shared layout `resources/views/layouts/app.blade.php`.
- Home prompt submit uses `fetch('/api/generate-video')` from `home.blade.php` and sends JSON payload `{ "prompt": "..." }`.
- API endpoint `POST /api/generate-video` maps to invokable `App\Http\Controllers\GenerateVideoController`.
- Request validation is centralized in `App\Http\Requests\GenerateVideoRequest`.
- Video persistence direction is prepared by migration `database/migrations/2026_02_27_122241_create_video_generations_table.php` (`video_generations` table with status/job/url/error fields).

## Developer workflows
- Install deps: `composer install` and `npm install`.
- Run app: `php artisan serve`.
- Frontend dev/build: `npm run dev` / `npm run build`.
- Run tests: `php artisan test` (feature tests currently live in `tests/Feature/ExampleTest.php`).
- Run migrations: `php artisan migrate` (new table migration exists for video generations).

## Code conventions used in this repo
- Keep existing route style when touching nearby code:
  - Web auth and page guards are inline closures using `if (! $request->session()->get('is_logged_in'))`.
  - API route uses controller class references (e.g., `GenerateVideoController::class`).
- Prefer invokable controllers for single-action endpoints (`__invoke`).
- Put validation in `FormRequest` classes under `app/Http/Requests`.
- Keep UI changes aligned with existing inline CSS token style in Blade files (shared CSS variables in layout).
- When adding nav pages, extend `layouts.app` and rely on `request()->routeIs(...)` for sidebar active state.

## Integration points and gotchas
- `/home` currently posts to API directly via JavaScript; avoid reintroducing server-form handling unless intentionally needed.
- There is still a legacy web route `POST /home/prompt` in `routes/web.php`; treat it as transitional unless removed explicitly.
- Current API controller returns a temporary debug payload echo; replace this when real generation integration starts.
- `auth:sanctum` is only used for `/api/user`; `generate-video` is presently open unless middleware is added.

## When implementing new work
- Follow existing file placement:
  - Controllers: `app/Http/Controllers`
  - Form requests: `app/Http/Requests`
  - Views: `resources/views`
  - Routes: `routes/web.php` and `routes/api.php`
- For API changes, update endpoint + FormRequest + frontend caller together to keep payload contracts in sync.
